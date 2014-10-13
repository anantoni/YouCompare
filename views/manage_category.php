<?php
        /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/
        include_once "db_includes/user.php";
        include_once "db_includes/category.php";
        include_once "db_includes/DB_DEFINES.php";
        include_once "db_includes/attribute.php";
        

	class manage_categoryPage {
            
            public function create_content() {

                    $category = "";
                    $category_id;
                    $category_name = "";
                    $category_description = "";
                    $category_keywords = "";
                    $public_checked = "";
                    $private_checked = "";
                    $secondary_button = "";

                    
                    
                    if ( isset( $_REQUEST['id'] ) ) {
                        
                        $category_id = $_REQUEST['id'];
                        
                        if ( $category_id < 1 ) 
                                header("Location: ./index.php");
                        
                        session_start();
                        $category = new category($category_id);
                        
                        if ( isset( $_SESSION ) ) {                                                                                    //An uparxei SESSION
                            
                            if ( isset( $_SESSION['username'] ) ) {                                                                      //An uparxei SESSION['username']
                                
                                $category->can_access( $_SESSION['username'], SUB_MODERATOR );
                                if ( $category->get_errno() != DB_OK )   {                                          //Elegxos an exei dikaiwma na dhmiourhgsei kathgoria

                                    $category->can_access( $_SESSION['username'], CATEGORY_MEMBER );
                                    if ( $category->get_errno() == DB_OK )
                                            header("Location: ./browseCategory.php?cat_id="+$category_id);        //redirect to browse category
                                    else 
                                            header("Location: ./index.php");
                                }
                                else {
                                        $category->can_access( $_SESSION['username'], MODERATOR );
                                        if ( $category->get_errno() == DB_OK ) 
                                            $secondary_button = "<span id=\"deleteCategory\"> Delete Category </span>";
                                        
                                        else 
                                            $secondary_button = "<span id=\"backToMyCategories\" onclick=\"window.location='my_categories.php?';\"> My Categories </span>";
                                        
                                }
                                   
                              
                            }
                            else
                                header("Location: ./index.php");
                        }
                        else
                            header("Location: ./index.php");

                        $attribute_array = $category->get_attributes( 0 );                             //get category attributes as array
                        $category_name = $category->get_name();
                        $category_description = $category->get_description();
                        $category_keywords_array = $category->get_keywords();
                        
                        if ( $category->is_open() ) 
                                $public_checked = "checked";
                        else 
                                $private_checked = "checked";
                        
                        if ( count( $category_keywords_array ) ) {
                            
                                $category_keywords = $category_keywords_array[0];
                                for ( $i = 1 ; $i < count( $category_keywords_array ) ; $i++ )
                                        $category_keywords .= ",".$category_keywords_array[$i];

                        }
                        
                        $this->content = "<div id=\"mainContainer\" align=\"center\" >
                                                      
                                <span id=\"toBrowseCategory\" onclick=\"window.location='browseCategory.php?cat_id=".$category_id."';\">Browse Category</span> <h2 style=\"font-size: 26px;\"> <span style=\"font-weight: bold;\">".$category_name ."</span> - Edit Category info and Attributes</h2>".$secondary_button." 
                                <hr>
                                <br>
                                <p class=\"trigger\">General category info: <span id=\"visible_info\">[Hide]</span> <span id=\"general_info\"> </span> </p> 

                                <div id=\"create_category\">
                                                           
                                            <fieldset>
                                                <span id=\"legend\">Please enter your category details</span><br>

                                                <label for=\"category_name \" class=\"create_category_label\"> Category Name: </label>                         
                                                <input class=\"createCategoryName\" value=\"".$category_name."\" name=\"category_name\"type=\"text\" maxlength=\"100\" title=\"please specify the name of your category\"  placeholder=\"e.g Mobile Phones\" value=\"\" />
                                                <div class=\"createCategoryNameError\"> </div>
                                                <br>

                                                <ul><li><label for=\"description\" class=\"create_category_label\"> Category Description (optional): </label></li>
                                                <li><textarea class=\"createCategoryDescription\" name=\" description\" cols=40 rows=5 maxlength=\"500\" title=\"please specify the description of your category\" value=\"\" />".$category_description."</textarea></li>
                                                <li><div class=\"createCategoryDescriptionError\"></div></li> </ul>	
                                                <br>


                                                <label for=\"keywords\" class=\"create_category_label\"> Category Keywords (optional): </label>  
                                                <input class=\"createCategoryKeywords\" value=\"".$category_keywords."\" name=\"keywords\" type=\"text\" maxlength=\"500\" title=\"please specify the keywords of your category\" value=\"\" /> 
                                                <div class=\"createCategoryKeywordsError\"> </div>	
                                                <br>

                                                <label class=\"create_category_label\"> Category Type: </label>  
                                                <input class=\"createCategoryType\" type=\"radio\"  name=\"type\" value=\"1\" ".$public_checked."> Public  
                                                <input class=\"createCategoryType\" type=\"radio\"  name=\"type\" value=\"0\" ".$private_checked."> Private
                                                
                                                <br>
                                                <br>
                                                <ul>
                                                <li><label class=\"create_category_label\"> Upload category image: </label></li> 
                                                <li><div id=\"iframe_panel\">
                                                    <iframe id=\"image_iframe\" src=\"./catImgStatic.html\" frameborder=\"0\" style=\"height:75px; position: relative; top: -10px; padding:0;\"></iframe>
                                                </div></li>
                                                </ul>
                                           </fieldset>
                                           <div id=\"ButtonSection\"> <button id=\"createCategoryButton\" class=\"createCategoryButtonDisabled\"> <span>Confirm changes in general info</span> </button> 
                                                        <br><span id=\"editCategoryInfoResult\"> </span><br>
                                            </div>
                                                                                      
                                </div>

                                <br>
                                <button id=\"addCountableAttributeButton\" class=\"addAttributeButton\"><img src=\"images/add.png\" alt=\"+\"><span class=\"buttonInMsg\"> Add Countable Attribute </span></button>
                                <button id=\"addDistinctAttributeButton\" class=\"addAttributeButton\"><img src=\"images/add.png\" alt=\"+\"><span class=\"buttonInMsg\"> Add Distinct Attribute </span></button>
                                <button id=\"addUncomparableAttributeButton\" class=\"addAttributeButton\"><img src=\"images/add.png\" alt=\"+\"><span class=\"buttonInMsg\"> Add Uncomparable Attribute </span></button>
                                <div id=\"deleteCategoryResult\"> </div>
                                <div id=\"boxes\" align=\"center\">

                                <div id=\"AddCountableAttributeModalWindow\" class=\"ModalWindow\">

                                        <label class=\"create_category_label\"> Attribute Name: </label>                         
                                        <input id=\"countableAttributeName\" type=\"text\" maxlength=\"50\" title=\"please specify the name of your attibute\" value=\"\" />
                                        <div class=\"countableAttributeNameError\"> </div>

                                        <ul><li><label class=\"create_category_label\"> Attribute Description (optional): </label></li>  
                                        <li><textarea id=\"countableAttributeDescription\" name=\"description\" cols=20 rows=5 maxlength=\"500\" title=\"please specify the description of this attribute\" value=\"\" /> </textarea></li>
                                        <li><div class=\"countableAttributeDescriptionError\"> </div></li></ul>

                                        <label class=\"create_category_label\" > Attribute Minimum Value: </label>                         
                                        <input id=\"countableAttributeMinValue\" type=\"text\" maxlength=\"11\" title=\"please specify the minimum value of your attibute\" value=\"\" />
                                        <div class=\"countableAttributeMinValueError\"> </div>
                                        <br>

                                        <label class=\"create_category_label\"> Attribute Maximum Value: </label>                         
                                        <input id=\"countableAttributeMaxValue\" type=\"text\" maxlength=\"11\" title=\"please specify the comparison type of your attibute\" value=\"\" />
                                        <div class=\"countableAttributeMaxValueError\"> </div>
                                        <br>

                                        <label class=\"create_category_label\"> Comparison Type: </label>                         
                                        <select id=\"countableAttributeComparisonType\"> 
                                                <option value=\"1\">Less is better</option>
                                                <option value=\"0\">More is better</option>
                                                <option value=\"2\">Average is better</option> 
                                        </select>  
                                        <div class=\"countableAttributeValueComparisonError\"> </div>

                                        <br>
                                        <label class=\"create_category_label\"> Filterable: </label>  
                                        <input id=\"countableFilterableYes\" class=\"countableFilterable\" type=\"radio\"  name=\"countablefilterable\" value=\"1\"> Yes  </input>
                                        <input id=\"countableFilterableNo\" class=\"countableFilterable\" type=\"radio\"  name=\"countablefilterable\" value=\"0\" > No  </input>

                                        <br>
                                        <br>
                                        <div id=\"ButtonSection\"><button id=\"saveCountableAttributeButton\" class=\"saveAttributeButton\"> <span>Confirm addition</span></button></div>
                                        <br>
                                        <button class=\"closeButton\"><img src=\"images/close.png\"></button>                                                                                                    


                                 </div>

                                 <div id=\"AddDistinctAttributeModalWindow\" class=\"ModalWindow\">

                                        <label class=\"create_category_label\"> Attribute Name: </label>                         
                                        <input id=\"distinctAttributeName\" maxlength=\"50\" type=\"text\" title=\"please specify the name of your attibute\" value=\"\" />
                                        <div class=\"distinctAttributeNameError\"> </div>
                                        <br>

                                        <ul><li><label class=\"create_category_label\"> Attribute Description (optional): </label></li>  
                                        <li><textarea id=\"distinctAttributeDescription\" name=\"description\" cols=20 rows=5 maxlength=\"500\" title=\"please specify the description of this attribute\" value=\"\" /> </textarea></li>
                                        <li><div class=\"distinctAttributeDescriptionError\"> </div></li></ul>

                                        <label class=\"create_category_label\"> Filterable: </label>  
                                        <input id=\"distinctFilterableYes\" class=\"distinctFilterable\" type=\"radio\"  name=\"distinctfilterable\" value=\"1\"> Yes   </input>
                                        <input id=\"distinctFilterableNo\" class=\"distinctFilterable\" type=\"radio\"  name=\"distinctfilterable\" value=\"0\"> No </input>
                                        <br>

                                        <button id=\"addDistinctValueButton\" class=\"distinctValueButton\"><span>Add Value-Weight</span></button>
                                        <button id=\"removeDistinctValueButton\" class=\"distinctValueButton\"><span>Remove Value-Weight</span></button>
                                        <br>
                                        <div id=\"Values-Weights\">

                                        </div>
                                        <br>
                                        <div id=\"ButtonSection\"><button id=\"saveDistinctAttributeButton\" class=\"saveAttributeButton\"><span>Confirm addition</span></button></div>
                                        <br>
                                        <button class=\"closeButton\"><img src=\"images/close.png\"></button>

                                 </div>

                                 <div id=\"AddUncomparableAttributeModalWindow\" class=\"ModalWindow\">

                                        <label class=\"create_category_label\"> Attribute Name: </label>                         
                                        <input id=\"uncomparableAttributeName\" type=\"text\" maxlength=\"50\" title=\"please specify the name of your attibute\" value=\"\" />
                                        <div class=\"uncomparableAttributeNameError\"> </div>                                                                
                                       <br>

                                        <ul><li><label class=\"create_category_label\"> Attribute Description (optional): </label></li>  
                                        <li><textarea id=\"uncomparableAttributeDescription\" name=\"description\" cols=20 rows=5 maxlength=\"500\" title=\"please specify the description of this attribute\" value=\"\" /> </textarea></li>
                                        <li><div class=\"uncomparableAttributeDescriptionError\"> </div></li></ul>

                                        <label class=\"create_category_label\"> Filterable: </label>  
                                        <input id=\"uncomparableFilterableYes\" class=\"uncomparableFilterable\" type=\"radio\"  name=\"uncomparablefilterable\" value=\"1\"> Yes  </input> 
                                        <input id=\"uncomparableFilterableNo\" class=\"uncomparableFilterable\" type=\"radio\"  name=\"uncomparablefilterable\" value=\"0\"> No  </input>
                                        <br>
                                        <br>
                                        <div id=\"ButtonSection\"><button id=\"saveUncomparableAttributeButton\" class=\"saveAttributeButton\"><span>Confirm addition</span></button></div>
                                        <br>
                                        <button class=\"closeButton\"><img src=\"images/close.png\"></button>

                                 </div>
                                 
                                 </div>
                                 <div id=\"Attributes\">

                                    <div id=\"CountableAttributes\">
                                    <p id=\"countable_toggle\" class=\"attribute_toggle\"> Countable Attributes: <span id=\"countable_visible\">[Hide]</span></p>

                                    <table id=\"CountableTable\" class=\"Table\">
                                            <tr>
                                                <th scope=\"col\" style=\"width: 350px;\">Name</th>
                                                <th scope=\"col\">Description</th>
                                                <th scope=\"col\" style=\"width: 100px;\">Min Value</th>
                                                <th scope=\"col\" style=\"width: 100px;\">Max Value</th>
                                                <th scope=\"col\" style=\"width: 120px;\">Optimal Value</th>
                                                <th scope=\"col\" style=\"width: 120px;\">Filterable</th>
                                                <th scope=\"col\" style=\"width: 120px;\">Edit</th>
                                                <th scope=\"col\" style=\"width: 120px;\">Remove</th>
                                            </tr>";
                                            for ( $i = 0 ; $i < count( $attribute_array ) ; $i++ ) {
                                                
                                                if ( $attribute_array[$i]->comparability == COUNTABLE ) {
                                                    
                                                    if ( $attribute_array[$i]->type_elements["Comparison Type"] == 1 ) 
                                                        $comparison_type = "min";
                                                    
                                                    else if ( $attribute_array[$i]->type_elements["Comparison Type"] == 0 ) 
                                                        $comparison_type = "max";
                                                   
                                                    else     
                                                        $comparison_type = "average";
                                                    
                                                    if ( $attribute_array[$i]->is_filterable == 0 )
                                                        $is_filterable = "No";
                                                    else 
                                                        $is_filterable = "Yes";
                                                            
                                                    $this->content .= "<tr class=\"alt\" id=\"attribute".$i."\"><th id=\"gui_id".$i."\" class=\"".$attribute_array[$i]->id."\" scope=\"row\">".$attribute_array[$i]->name."</th> <td> ".$attribute_array[$i]->description." </td><td> ".$attribute_array[$i]->type_elements["Lower Limit"]." </td><td> ".$attribute_array[$i]->type_elements["Upper Limit"]." </td><td> ".$comparison_type." </td><td> " .$is_filterable. "</td><td> <span id=\"editAttribute".$i."\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute".$i."\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td></tr> ";
                                                         
                                                }
                                            }
                                            $this->content .= "
                                            </table>
                                            </div>
                                            <div id=\"editCountableAttributeResult\"> </div>
                                            
                                            <div id=\"DistinctAttributes\">  
                                                <p id=\"distinct_toggle\" class=\"attribute_toggle\"> Distinct Attributes: <span id=\"distinct_visible\">[Hide]</span></p>
                                                    
                                                        <table id=\"DistinctTable\" class=\"Table\">
                                                            <tr>
                                                                <th scope=\"col\" style=\"width: 350px;\">Name</th>
                                                                <th scope=\"col\">Description</th>
                                                                <th scope=\"col\">Values-Weights</th>
                                                                <th scope=\"col\" style=\"width: 120px;\">Filterable</th>
                                                                <th scope=\"col\" style=\"width: 120px;\">Edit</th>
                                                                <th scope=\"col\" style=\"width: 120px;\">Remove</th>
                                                            </tr>";
                                            for ( $i = 0 ; $i < count( $attribute_array ) ; $i++ ) {
                                            
                                                if ( $attribute_array[$i]->comparability == DISTINCT ) {
                                                                                                       
                                                    $values_weights_string = $attribute_array[$i]->type_elements[0]["Value"]."=>".$attribute_array[$i]->type_elements[0]["Preference"];
                                                    
                                                    for ( $j = 1 ; $j < count( $attribute_array[$i]->type_elements ) ; $j++ )
                                                        $values_weights_string .= ",".$attribute_array[$i]->type_elements[$j]["Value"]."=>".$attribute_array[$i]->type_elements[$j]["Preference"];
                                                    
                                                    if ( $attribute_array[$i]->is_filterable == 0 )
                                                        $is_filterable = "No";
                                                    else 
                                                        $is_filterable = "Yes";
                                                            
                                                    $this->content .= "<tr class=\"alt\" id=\"attribute".$i."\"><th id=\"gui_id".$i."\" class=\"".$attribute_array[$i]->id."\" scope=\"row\">".$attribute_array[$i]->name."</th> <td> ".$attribute_array[$i]->description." </td><td> ".$values_weights_string." </td><td> " .$is_filterable. "</td><td> <span id=\"editAttribute".$i."\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute".$i."\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td></tr> ";
                                                         
                                                }
                                            }
                                           $this->content .= "
                                           </table>
                                           </div>
                                           <div id=\"editDistinctAttributeResult\"> </div>
                                            
                                           <div id=\"UncomparableAttributes\">
                                                <p id=\"uncomparable_toggle\" class=\"attribute_toggle\"> Uncomparable Attributes: <span id=\"uncomparable_visible\">[Hide]</span></p>
                                                   
                                                        <table id=\"UncomparableTable\" class=\"Table\">
                                                            <tr>
                                                                <th scope=\"col\" style=\"width: 350px;\">Name</th>
                                                                <th scope=\"col\">Description</th>
                                                                <th scope=\"col\" style=\"width: 120px;\">Filterable</th>
                                                                <th scope=\"col\" style=\"width: 120px;\">Edit</th>
                                                                <th scope=\"col\" style=\"width: 120px;\">Remove</th>
                                                            </tr>";
                                            for ( $i = 0 ; $i < count( $attribute_array ) ; $i++ ) {
                                            
                                                if ( $attribute_array[$i]->comparability == UNCOMPARABLE ) {
                                                    
                                                    if ( $attribute_array[$i]->is_filterable == 0 )
                                                        $is_filterable = "No";
                                                    else 
                                                        $is_filterable = "Yes";
                                                            
                                                    $this->content .= "<tr class=\"alt\" id=\"attribute".$i."\"><th id=\"gui_id".$i."\" class=\"".$attribute_array[$i]->id."\" scope=\"row\">".$attribute_array[$i]->name."</th> <td> ".$attribute_array[$i]->description." </td><td> " .$is_filterable. "</td><td> <span id=\"editAttribute".$i."\" class=\"editAttribute\">Edit</span></td><td><span id=\"removeAttribute".$i."\" class=\"removeAttribute\"><img class=\"remove_image\" src=\"images/close.png\">Remove</span></td></tr> ";
                                                         
                                                }
                                            }
                                            $this->content .= "</table>
                                            </div><div id=\"editUncomparableAttributeResult\"> </div>
                                        </div>
                                        <div id=\"deleteAttributeResult\"> </div>
                                    </div>";
                            }                   
                    }

                    public function get_content() {
                                    return $this->content;
                    }
        }
?>