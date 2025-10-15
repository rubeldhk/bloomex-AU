<script src="../../resources/jquery.js"></script>
<script>
    $( function() {

            var order_id = "<?php echo $_REQUEST['order_id'];?>";
            var sender = "<?php echo $_REQUEST['sender'];?>";
            $.post( "connectstoauspostapi.php", { action: "deleteshipment", order_id: order_id,sender:sender },
                function( dataofconfirm ) {
                    var obj = jQuery.parseJSON( dataofconfirm);
                    if(obj.error){
                        document.write("<span style='color:red'>"+obj.error+"</span>")
                    }else{
                        document.write(obj.msg)
                    }
                });



    })
    </script>