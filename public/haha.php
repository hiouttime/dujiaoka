<?php

file_put_contents("api.txt",json_encode($_REQUEST,JSON_UNESCAPED_UNICODE));
echo "ok!!";