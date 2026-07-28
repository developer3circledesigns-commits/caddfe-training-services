<?php

$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = 'rootpassword';
$cfg['Servers'][$i]['host'] = 'db';
$cfg['Servers'][$i]['port'] = '3306';
$cfg['Servers'][$i]['AllowNoPassword'] = true;
$cfg['Servers'][$i]['hide_db'] = '(information_schema|performance_schema|mysql|sys)';
$cfg['LoginCookieValidity'] = 86400;
$cfg['SessionSavePath'] = '/sessions';
