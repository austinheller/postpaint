document.addEventListener('DOMContentLoaded', function() {

  var textArea = document.getElementById('postpaint-css-code');
  if(textArea && CodeMirror) {

    var editor = CodeMirror.fromTextArea(textArea, {
      lineNumbers: true,
      mode: 'text/css'
    })

  }

});
