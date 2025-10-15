<table>
    <tr>
        <td>Click here to start the save.</td>
        <td><input type="submit" name="save" id="save" value="Save"></td>
    </tr>
    <tr>
        <td colspan="2"><div id="Savemessage"></div></td>
    </tr>
</table>
<script>
    $j('#save').click(function(){
        $j.post("components/com_legacy/SaveDataHandler.php",
            {},
            function(data){$j('#Savemessage').html(data.message)},
            "json"
        );
    });
</script>