<?php

session_start();

#resets the session
session_unset();

#stops the session and sends the user back to the log in page
session_destroy();
echo"<script>window.location.href = 'log_in.php';</script>";

