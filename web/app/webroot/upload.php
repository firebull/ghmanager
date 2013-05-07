<?php

/*
 * Created on 07.06.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
print_r($_FILES);
?>
<form enctype="multipart/form-data" action="upload.php" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
    <!-- Name of input element determines name in $_FILES array -->
    Send this file: <input name="userfile" type="file" />
    <input type="submit" value="Send File" />
</form>
<form enctype="multipart/form-data" id="UserUploadAvatarForm" method="post" action="/dev/TeamServer/client/users/uploadAvatar/11" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST"></div>		<input type="file" name="data[User][avatar]" id="UserAvatar">		<div class="submit"><input url="" value="Submit" id="submit1602906339"  type="submit"></div></form>