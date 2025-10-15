<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<title>Get Users Emails</title>
</head>
<body>  
<form  enctype="multipart/form-data" id="file-form" style="margin-right: 30px;margin-top: 20px;" action="getusersemails.php" method="POST">
    <input type="file" id="file-select" name="file_numbers" multiple/>
    <button type="submit" id="upload-button">Upload</button>
    <div style="color:red;display: none;" id="error_text">File is empty or has wrong format, please try another file</div>
 </form> 
</body>
</html>