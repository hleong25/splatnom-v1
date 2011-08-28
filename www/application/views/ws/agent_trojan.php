<?php
if ($ask_passcode === true)
{
?>
<form id="login" method="post" action='/ws/agent_trojan' style='text-align: center;'>
    <span>Passcode: </span>
    <input id="passcode" type="password" name="passcode" />
    <input type="submit" value="Infect!" />
</form>
<?php
} // if ($ask_passcode === true)
else
{
?>

<?php
} // else