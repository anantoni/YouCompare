<?php
        /*******************************
            Software Engineering 2011 - YouCompare Website
            Code developed by Anastasios Antoniadis
            May-June 2011
            *******************************/
    
                                                                
        class create_categoryPage { 
                private $content;

                public function create_content() {
                            session_start();
                            if ( !isset( $_SESSION['username'] ) ) {
                                header("Location: ./index.php");
                            }
                            
                            $this->content = "<div id=\"mainContainer\" align=\"center\" >
                                
                                                    
                                                    <h2 style=\"font-size: 30px; font-weight: bold;\"> Create Category </h2>
                                                    <hr>
                                                    <p class=\"trigger\">General category info: <span id=\"visible_info\">[Hide]</span> <span id=\"general_info\"> </span> </p> 
                                                                                                     
                                                    <div id=\"create_category\">
                                                           
                                                            <fieldset>
                                                                <span id=\"legend\">Please enter your category details: </span><br>
                                                                                                                    
                                                                <label for=\"category_name \" class=\"create_category_label\"> Category Name: </label>                         
                                                                <input class=\"createCategoryName\" name=\"category_name\"type=\"text\" maxlength=\"100\" title=\"please specify the name of your category\"  placeholder=\"e.g Mobile Phones\" value=\"\" />
                                                                <div class=\"createCategoryNameError\"> </div>
                                                                <br>
                                                                
                                                                <ul><li><label for=\"description\" class=\"create_category_label\"> Category Description (optional): </label></li>
                                                                <li><textarea class=\"createCategoryDescription\" name=\"description\" cols=40 rows=5 maxlength=\"500\" title=\"please specify the description of your category\" value=\"\" /> </textarea></li>
                                                                <li><div class=\"createCategoryDescriptionError\"></div></li> </ul>	
                                                                <br>
                                                                
                                                                
                                                                <label for=\"keywords\" class=\"create_category_label\"> Category Keywords (optional): </label>  
                                                                <input class=\"createCategoryKeywords\" name=\"keywords\" type=\"text\" maxlength=\"500\" title=\"please specify the keywords of your category\" value=\"\" /> 
                                                                <div class=\"createCategoryKeywordsError\"> </div>	
                                                                <br>
                                                                 
                                                                <label class=\"create_category_label\"> Category Type: </label>  
                                                                <input class=\"createCategoryType\" type=\"radio\"  name=\"type\" value=\"1\" checked> Public  
                                                                <input class=\"createCategoryType\" type=\"radio\"  name=\"type\" value=\"0\"> Private
                                                                <br>
                                                                <br>
                                                                <ul>
                                                                <li><label class=\"create_category_label\"> Upload category image: </label></li> 
                                                                <li><div id=\"iframe_panel\">
                                                                    <iframe id=\"image_iframe\" src=\"./catImgStatic.html\" frameborder=\"0\" style=\"height:75px; position: relative; top: -10px; padding:0;\"></iframe>
                                                                </div></li>
                                                                </ul>

                                                           </fieldset>
                                                       
                                                           <div id=\"template_panel\" align=\"center\">
                                                            <label for=\"template\" ><b>You can pick a category to load its attribute template: </b></label><br> 
                                                            <input id=\"getCategoryTemplateName\" name=\"template\" type=\"text\" maxlength=\"100\" placeholder=\"CategoryTemplate\" value=\"\" />
                                                            <button id=\"getCategoryTemplateButton\"> <span> Get Category template </span> </button>
                                                            <span id=\"categoryTemplateError\"></span>
                                                            <br>
                                                            <br>
                                                            <b>OR <br><br>Add your own attributes: </b>
                                                            <br>
                                                            <br>
                                                            </div>
                                                            </div>
                                                            <br>
                                                            <br>
                                                            <button id=\"addCountableAttributeButton\" class=\"addAttributeButton\"><img src=\"images/add.png\" alt=\"+\"><span class=\"buttonInMsg\"> Add Countable Attribute </span></button>
                                                            <button id=\"addDistinctAttributeButton\" class=\"addAttributeButton\"><img src=\"images/add.png\" alt=\"+\"><span class=\"buttonInMsg\"> Add Distinct Attribute </span></button>
                                                            <button id=\"addUncomparableAttributeButton\" class=\"addAttributeButton\"><img src=\"images/add.png\" alt=\"+\"><span class=\"buttonInMsg\"> Add Uncomparable Attribute </span></button>
                                                            <br>
                                                            <br>

                                                            <button id=\"createCategoryButton\" class=\"createCategoryButtonDisabled\"> <span> Create Category </span> </button>
                                                            <div id=\"createCategoryResult\"> </div>
                                                        
                                        
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
                                                            <option value=\"min\">Less is better</option>
                                                            <option value=\"max\">More is better</option>
                                                            <option value=\"average\">Average is better</option> 
                                                    </select>  
                                                    <div class=\"countableAttributeValueComparisonError\"> </div>
                                                    
                                                    <br>
                                                    <label class=\"create_category_label\"> Filterable: </label>  
                                                    <input id=\"countableFilterableYes\" class=\"countableFilterable\" type=\"radio\"  name=\"countablefilterable\" value=\"1\" checked> Yes  
                                                    <input id=\"countableFilterableNo\" class=\"countableFilterable\" type=\"radio\"  name=\"countablefilterable\" value=\"0\" > No
                                                    
                                                    <br><br>
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
                                                    <input id=\"distinctFilterableYes\" class=\"distinctFilterable\" type=\"radio\"  name=\"distinctfilterable\" value=\"1\" checked> Yes  
                                                    <input id=\"distinctFilterableNo\" class=\"distinctFilterable\" type=\"radio\"  name=\"distinctfilterable\" value=\"0\"> No
                                                    <br>
                                                    
                                                    <button id=\"addDistinctValueButton\" class=\"distinctValueButton\"><span>Add Value-Weight</span></button>
                                                    <button id=\"removeDistinctValueButton\" class=\"distinctValueButton\"><span>Remove Value-Weight</span></button>
                                                    <br>
                                                    <div id=\"Values-Weights\">
                                                    
                                                    </div>
                                                    <br><br>
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
                                                    <input id=\"uncomparableFilterableYes\" class=\"uncomparableFilterable\" type=\"radio\"  name=\"uncomparablefilterable\" value=\"1\" checked> Yes  
                                                    <input id=\"uncomparableFilterableNo\" class=\"uncomparableFilterable\" type=\"radio\"  name=\"uncomparablefilterable\" value=\"0\"> No 
                                                    <br><br>
                                                    
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
                                                            </tr>
                                                        </table>
                                            </div>
                                            
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
                                                            </tr>
                                                        </table>
                                            </div>
                                            
                                            <div id=\"UncomparableAttributes\">
                                                <p id=\"uncomparable_toggle\" class=\"attribute_toggle\"> Uncomparable Attributes: <span id=\"uncomparable_visible\">[Hide]</span></p>
                                                   
                                                        <table id=\"UncomparableTable\" class=\"Table\">
                                                            <tr>
                                                                <th scope=\"col\" style=\"width: 350px;\">Name</th>
                                                                <th scope=\"col\">Description</th>
                                                                <th scope=\"col\" style=\"width: 120px;\">Filterable</th>
                                                                <th scope=\"col\" style=\"width: 120px;\">Edit</th>
                                                                <th scope=\"col\" style=\"width: 120px;\">Remove</th>
                                                            </tr>
                                                        </table>
                                            </div>
                                        </div>         
                                    </div>";
                    }                   
                    
                    public function get_content() {
                                    return $this->content;
                    }
        }
?>