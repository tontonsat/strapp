// simple function to correct bootstrap form theme: label content not updating after file select in file input 
// is called in profile template for avatar upload (onChange)
var getOutputFile = () => { 
    var value = $("#upload_imageFile_file").val().replace('C:\\fakepath\\', '').trim()
    
    $("#upload_imageFile_file").next(".custom-file-label").html(value)
}
var getOutputFileVote = () => { 
    var value = $("#vote_imageFile_file").val().replace('C:\\fakepath\\', '').trim()
    
    $("#vote_imageFile_file").next(".custom-file-label").html(value)
}