<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <title>JavaScript form input mask demonstration</title>
   <meta name="description" content="Demonstrates how unobtrusive JavaScript can implement input masks in HTML form elements" />
   <script type="text/javascript" language="javascript" src="js/prototype.js" ></script>
   <script type="text/javascript" language="javascript" src="js/html-form-input-mask.js"></script>
   <style type="text/css">
   input.text, textarea, select {
      font-size:    1.1em;
      line-height:  1.3em;
      border-color: #7C7C7C #C3C3C3 #DDD;
      border-style: solid;
      border-width: 1px;
      background:   #FFF url(fieldbg.gif) repeat-x top;
   }
   label {
      width:      400px;
      display:    block;
      text-align: right;
   }
   </style>

</head>

<body onload="Xaprb.InputMask.setupElementMasks()" >

<h1>JavaScript form input mask demonstration</h1>

<p>The following form fields use unobtrusive JavaScript to implement "input
masks."  The maximum field length is automatically limited to the length of
the mask.  The characters you can enter into the fields are constrained, and
separators are automatically added, but no actual value checking is done.  For
example, the date input is constrained to the general format for a date, but
you can enter an invalid date into the field.</p>

<form action="" method="get">

<label for="input_date">Date (US format)
   <input id="input_date" type="text" class="text input_mask mask_date_us" />
</label>
<label for="input_time">Time
   <input id="input_time" type="text" class="text input_mask mask_time" />

</label>
<label for="input_phone">Phone number
   <input id="input_phone" type="text" class="text input_mask mask_phone" />
</label>

</form>

</body>
</html>
