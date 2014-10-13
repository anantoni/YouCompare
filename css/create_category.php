<?php

        class create_categoryPage { 
                    private $content;

                    public function create_content() {
                                                             
                                $this->content = "<div id=\"mainContainer\" align=\"center\" >
                                                      
                                                        <h2 style=\"font-size: 30px; font-weight: bold;\"> Create Category </h2>
                                                        <hr>
                                                        <div id=\"create_category\">  
                                                                
                                                        <br>
                                                        
                                                        <br>

                                                        <label> Category Name: </label>                         
                                                        <input class=\"createCategoryName\" type=\"text\" title=\"please specify the name of your category\"  placeholder=\"e.g Mobile Phones\" value=\"\" />
                                                        <div class=\"createCategoryNameError\"> </div>
                                                        <br>
                                                        <label> Category Description (optional): </label>  
                                                        <input class=\"createCategoryDescription\" type=\"textarea\" title=\"please specify the description of your category\" value=\"\" /> 
                                                        <div class=\"createCategoryDescriptionError\"> </div>	
                                                        <br>
                                                        <label> Category Keywords (optional): </label>  
                                                        <input class=\"createCategoryKeywords\" type=\"textarea\" title=\"please specify the keywords of your category\" value=\"\" /> 
                                                        <div class=\"createCategoryKeywordsError\"> </div>	
                                                        <br>
                                                        <label> Category Type: </label>  
                                                        <input class=\"createCategoryType\" type=\"text\" title=\"please specify your last name\" placeholder=\"e.g Rambo\" value=\"\" /> 
                                                        <div class=\"createCategoryTypeError\"> </div>
                                                        <br>
                                                        
</div>
                                                        <button id=\"addCountableAttribute\" class=\"addAttributeButton\"> Add Countable Attribute </button>
                                                        <button id=\"addDistinctAttribute\" class=\"addAttributeButton\"> Add Distinct Attribute </button>
                                                        <button id=\"addUncomparableAttribute\" class=\"addAttributeButton\"> Add Uncomparable Attribute </button>
                                                        <br>
                                                        <br>
                                                        <br>
                                                
                                                
                                                        <button id=\"createCategoryButton\" class=\"createCategoryButtonDisabled\"> <span> Create Category </span> </button>
                                                        <br>
                                                        <div id=\"registrationResult\"> </div>
                                                        <br><br>
                                               
                                        </div>";
                    }
                    
                    
                    public function get_content() {
                        return $this->content;
                    }
        }
?>

