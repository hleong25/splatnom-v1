<?php

class ut_gohome extends Unit_Test
{
    protected function getUrl()
    {
        return 'http://www.gogomenu.com/home/main';
    }

    protected function validate()
    {
        return true;
    }
}
