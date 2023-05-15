$("#product_imageFile_file").click(function(e){
    var fileName =$("input[name='product[imageFile][file]']").val();
    if(fileName.length != 0){
    }
    //On continue, l'input n'est pas vide
    else{
        $("#fichier").hide();
    }
})
