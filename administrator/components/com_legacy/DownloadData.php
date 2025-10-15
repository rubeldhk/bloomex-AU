<table>
    <tr>
        <td width="200">Click here to start the download.</td>
        <td><input type="submit" name="download" id="download" value="Download"></td>
    </tr>
    <tr>
        <td colspan="2"><div id="ifrDiv"></div></td>
    </tr>
</table>
<script>
    $j('#download').click(function(){
        $j('#ifrDiv').html("<iframe width='1700' height='500' src='<?php echo $path; ?>'></iframe>");
    });
</script>