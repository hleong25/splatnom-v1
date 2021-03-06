<?php
require_once('Mail.php');
require_once('Mail/mime.php');
require_once('Mail/mime.php');

class MailModel
    extends Model
{
    public $DEFAULT_EMAIL = 'support@splatnom.com';
    public $MAX_ATTEMPTS = 5;

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
        $max_rows = 50;
        $crlf = "\n";

        // these should be in a config file...
        $host = 'ssl://smtp.gmail.com';
        $port = '465';

        // these should be in a config file...
        $username = 'support@splatnom.com';
        $password = 'yellow25';

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

        $rst = $this->prepareAndExecute($query, array(':max_attempts'=>$this->MAX_ATTEMPTS), __FILE__, __LINE__);
        if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;

        $rows = $rst->fetchAll(PDO::FETCH_ASSOC);

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

    function get_pending_emails()
    {
        $query =<<<EOQ
            SELECT
                mail_id,
                ts,
                ms.mail_status,
                attempts,
                from_addy,
                to_addy,
                subject,
                message
            FROM tblEmailQueue eq
            INNER JOIN vMailStatus ms ON eq.status_id = ms.id
            WHERE status_id IN (SELECT id FROM vMailStatus WHERE mail_status != 'sent')
            AND attempts < :max_attempts
            ORDER BY ts ASC
EOQ;

        $rst = $this->prepareAndExecute($query, array(':max_attempts'=>$this->MAX_ATTEMPTS), __FILE__, __LINE__);
        if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;

        return $rst->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_email_src($email_id)
    {
        $query =<<<EOQ
            SELECT
                message
            FROM tblEmailQueue
            WHERE mail_id = :mail_id
EOQ;

        $rst = $this->prepareAndExecute($query, array(':mail_id'=>$email_id), __FILE__, __LINE__);
        if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;

        $row = $rst->fetch(PDO::FETCH_ASSOC);

        if (empty($row)) return '';
        return $row['message'];
    }
}
