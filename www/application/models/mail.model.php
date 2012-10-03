<?php
require_once('Mail.php');
require_once('Mail/mime.php');
require_once('Mail/mime.php');

class MailModel
    extends Model
{
    public $DEFAULT_EMAIL = 'support@splatnom.com';

    private static $m_email_sent = false;

    public function hasEmailSent()
    {
        return self::$m_email_sent;
    }

    function queue($from, $to, $subject, $message)
    {
        if (empty($from))
            $from = $this->DEFAULT_EMAIL;

        $query =<<<EOQ
            INSERT INTO tblEmailQueue
            SET
                status_id = (SELECT id FROM vMailStatus WHERE mail_status='pending'),
                from_addy = :from,
                to_addy = :to,
                subject = :subject,
                message = :message
EOQ;

        $params = array(
            ':from' => $from,
            ':to' => $to,
            ':subject' => $subject,
            ':message' => $message,
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;

        $mail_id = $this->lastInsertId();
        return $mail_id;
    }

    function process_queue()
    {
        self::$m_email_sent = true;

        $max_rows = 50;
        $max_attempts = 5;
        $crlf = "\n";

        // these should be in a config file...
        $host = 'ssl://smtp.gmail.com';
        $port = '465';

        // these should be in a config file...
        $username = 'support@splatnom.com';
        $password = 'alhambra1234';

        $smtp_cfg = array(
            'host'      => $host,
            'port'      => $port,
            'auth'      => true,
            'username'  => $username,
            'password'  => $password,
        );

        $mail = Mail::factory('smtp', $smtp_cfg);

        $query =<<<EOQ
            UPDATE tblEmailQueue
            SET status_id = (SELECT id FROM vMailStatus WHERE mail_status = 'sent')
            WHERE mail_id = :mail_id
EOQ;

        $prepareSent = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareSent) return false;

        $query =<<<EOQ
            UPDATE tblEmailQueue
            SET
                status_id = (SELECT id FROM vMailStatus WHERE mail_status = 'failed'),
                attempts = attempts+1
            WHERE mail_id = :mail_id
EOQ;

        $prepareFailed = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareFailed) return false;

        $query =<<<EOQ
            SELECT
                mail_id,
                from_addy,
                to_addy,
                subject,
                message
            FROM tblEmailQueue
            WHERE status_id IN (SELECT id FROM vMailStatus WHERE mail_status != 'sent')
            AND attempts < :max_attempts
            ORDER BY ts ASC
            LIMIT $max_rows
EOQ;

        $rst = $this->prepareAndExecute($query, array(':max_attempts'=>$max_attempts), __FILE__, __LINE__);
        if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;

        $rows = $rst->fetchAll();

        $sent_stats = array(
            'sent' => 0,
            'fail' => 0,
        );

        foreach ($rows as $row)
        {
            $mail_id    = $row['mail_id'];
            $from       = $row['from_addy'];
            $to         = $row['to_addy'];
            $subject    = $row['subject'];
            $message    = $row['message'];

            if (empty($from))
                $from = $this->DEFAULT_EMAIL;

            $headers = array(
                'From'          => $from,
                'To'            => $to,
                'Return-Path'   => $from,
                'Subject'       => $subject
            );

            $mime = new Mail_mime($crlf);
            $mime->setHTMlBody($message);
            $body = $mime->get();
            $headers = $mime->headers($headers);

            $send = $mail->send($to, $headers, $body);

            $rsts = array();
            if (PEAR::isError($send))
            {
                $sent_stats['fail']++;
                Util::logit('Mail error: '.$send->getMessage(), __FILE__, __LINE__);
                $rsts[] = $prepareFailed->bindValue(':mail_id', $mail_id);
                $rsts[] = $prepareFailed->execute();
                $this->areDbResultsGood($rst, __FILE__, __LINE__);
            }
            else
            {
                $rsts[] = $prepareSent->bindValue(':mail_id', $mail_id);
                $rsts[] = $prepareSent->execute();

                if ($this->areDbResultsGood($rst, __FILE__, __LINE__))
                {
                    $sent_stats['sent']++;
                }
                else
                {
                    $sent_stats['fail']++;
                }
            }
        }

        return $sent_stats;
    }

    function send_smtp($from, $to, $subject, $message)
    {
        // TODO: send this request to a queue so
        // it can be returned fast and then the
        // process queue can spend time sending
        // it out

        $crlf = "\n";

        if (empty($from))
            $from = $this->DEFAULT_EMAIL;

        $headers = array(
            'From' => $from,
            'To' => $to,
            'Return-Path' => $from,
            'Subject' => $subject
        );

        $host = 'ssl://smtp.gmail.com';
        $port = '465';

        $username = 'support@splatnom.com';
        $password = 'alhambra1234';

        $smtp_cfg = array();
        $smtp_cfg['host'] = $host;
        $smtp_cfg['port'] = $port;
        $smtp_cfg['auth'] = true;
        $smtp_cfg['username'] = $username;
        $smtp_cfg['password'] = $password;

        $mail = Mail::factory('smtp', $smtp_cfg);
        $mime = new Mail_mime($crlf);

        $mime->setHTMlBody($message);
        $body = $mime->get();
        $headers = $mime->headers($headers);

        self::$m_email_sent = true;
        $send = $mail->send($to, $headers, $body);

        if (PEAR::isError($send))
        {
            Util::logit('Mail error: '.$send->getMessage(), __FILE__, __LINE__);
            return false;
        }

        return true;
    }

    function send_tester($from, $to, $subject, $message)
    {
        $send = $this->send_smtp($from, $to, $subject, $message);

        return $send === true;
    }

    function grab_data($ctrl, $page, $params)
    {
        $view_page = $ctrl . DS . $page . '.php';
        $path = ROOT . DS . 'application' . DS . 'views';
        $file = $path . DS . $view_page;

        if (!file_exists($file))
        {
            Util::logit("Email view page not found: {$view_page}");
            return false;
        }

        ob_start();
            extract($params);
            include($file);
        $data = ob_get_clean();

        return $data;
    }
}
