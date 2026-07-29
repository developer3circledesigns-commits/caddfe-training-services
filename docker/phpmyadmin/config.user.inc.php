<?php

$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['host'] = 'db';
$cfg['Servers'][$i]['port'] = '3306';
$cfg['Servers'][$i]['AllowNoPassword'] = false;
$cfg['Servers'][$i]['hide_db'] = '(information_schema|performance_schema|mysql|sys)';
$cfg['LoginCookieValidity'] = 86400;
$cfg['SessionSavePath'] = '/sessions';
$cfg['blowfish_secret'] = 'caddfe_2024_bl0wF1sh_S3cr3t_K3y!@#$';
