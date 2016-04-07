<?php
$settingMemKey = shm_attach(123456788);
shm_remove_var($settingMemKey, 666666666);
