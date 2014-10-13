<?php

//creates the folders needed for the file system use once
function createFileSystem(){
    if(mkdir("./cat_images")){
        for($i=0;$i<20;$i++){
            if(!mkdir("./cat_images/".$i)){
                echo "Error while making file: ".$i;
                return;
            }
        }
    }
    else{
        echo "error while making file for category images";
        return;
    }

    if(mkdir("./ent_images")){
        for($i=0;$i<50;$i++){
            if(!mkdir("./ent_images/".$i)){
                echo "Error while making file: ".$i;
                return;
            }
        }
    }
    else{
        echo "error while making file for category images";
        return;
    }   
}


//creates a random string of size $length
function getRandomString($length){
    $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ1234567890";
    $validCharNumber = strlen($validCharacters);

    $result = "";

    for ($i = 0; $i < $length; $i++){
        $index = mt_rand(0, $validCharNumber - 1);
        $result .= $validCharacters[$index];
    }

    return $result;
}


//adds a category image to the file system. $name is the name of the input, $image is the $_FILES
function addCategoryImage($name, $image){
    if($image[$name]["tmp_name"]==NULL)
        return NULL;

    if ((($image[$name]["type"] == "image/gif")
     || ($image[$name]["type"] == "image/jpeg")
     || ($image[$name]["type"] == "image/pjpeg")
     ||  ($image[$name]["type"] == "image/png"))
     && ($image[$name]["size"] < 1000000)){            //200KB max size
        if ($image[$name]["error"] > 0){
            //echo "Return Code: " . $image[$name]["error"] . "<br />";
            return NULL;
        }
        else{
            $path="./cat_images/".rand(0, 19)."/";

            $old_name=$image[$name]["name"];
	
            $file_ext = substr($old_name, strripos($old_name, '.'));
            $image[$name]["name"]=getRandomString(20).$file_ext;
            //echo "New name: " . $image[$name]["name"] . "<br />";

            while(1){
                if (!file_exists($path.$image[$name]["name"])){	
                    move_uploaded_file($image[$name]["tmp_name"], $path.$image[$name]["name"]);
                    //echo "Stored in: ".$path.$image[$name]["name"];

                    return $path.$image[$name]["name"];
                }
                else
                    $image[$name]["name"]=getRandomString(20).$file_ext;
            }
        }
    }
    else{
        //echo "Invalid file";
        return NULL;
    }
}


//adds an entity image to the file system. $name is the name of the input, $image is the $_FILES
function addEntityImage($name, $image){
    if($image[$name]["tmp_name"]==NULL)
        return NULL;

    if ((($image[$name]["type"] == "image/gif")
     || ($image[$name]["type"] == "image/jpeg")
     || ($image[$name]["type"] == "image/pjpeg")
     ||  ($image[$name]["type"] == "image/png"))
     && ($image[$name]["size"] < 1000000)){            //200KB max size
        if ($image[$name]["error"] > 0){
            //echo "Return Code: " . $image[$name]["error"] . "<br />";
            return NULL;
        }
        else{
            $path="./ent_images/".rand(0, 49)."/";

            $old_name=$image[$name]["name"];

            $file_ext = substr($old_name, strripos($old_name, '.'));
            $image[$name]["name"]=getRandomString(20).$file_ext;
            //echo "New name: " . $image[$name]["name"] . "<br />";

            while(1){
                if (!file_exists($path.$image[$name]["name"])){
                    move_uploaded_file($image[$name]["tmp_name"], $path.$image[$name]["name"]);
                    //echo "Stored in: ".$path.$image[$name]["name"];

                    return $path.$image[$name]["name"];
                }
                else
                    $image[$name]["name"]=getRandomString(20).$file_ext;
            }
        }
    }
    else{
        //echo "Invalid file";
        return NULL;
    }
}


//deletes an image based on path
function deleteImage($path){
    if(unlink($path)==TRUE){
        //echo "Image deleted";
        return 0;
    }
    else{
        //echo "Bad path";
        return -1;
    }
}

?>