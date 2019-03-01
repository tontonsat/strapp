$('.ui.sidebar')
  .sidebar('toggle')
;
$('.ui.sidebar').first()
  .sidebar('attach events', '.open.button', 'show')
;
$('.open.button')
  .removeClass('disabled')
;