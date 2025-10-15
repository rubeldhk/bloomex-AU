function addContentTag(){
  var form = document.adminForm;
  var tempval=eval("document.adminForm.content_tag");
  if(form.contentid.options[form.contentid.selectedIndex].value != 0) {
    //alert(form.contentid.options[form.contentid.selectedIndex].value);
    var text = '[CONTENT id="' + form.contentid.options[form.contentid.selectedIndex].value + '"]';
    
    insertAtCursor(form.nl_content, text);

  }
  //form.contentid.options[0].selected = true;
}


function insertAtCursor(myField, myValue) {
  //IE support
  if (document.selection) {
    myField.focus();
    sel = document.selection.createRange();
    sel.text = myValue;
  }
  //MOZILLA/NETSCAPE support
  else if (myField.selectionStart || myField.selectionStart == '0') {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos)
    + myValue
    + myField.value.substring(endPos, myField.value.length);
  } else {
    myField.value += myValue;
  }
}