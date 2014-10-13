<?php
	
/*****************************************************************************\
|   Authors  : Zorbas Dimitrios  	1115 2007 00078,		    **|
|              Elissavet Sakellari	1115 2006 00152                     **|
|   contact : zorbash@hotmail.com, std06152@di.uoa.gr                       **|
|   Project: YouCompare Site - Software Engineering Course Spring 2011      **|
|   Description: backend part of results generation                         **|
|                                                                           **|   
\*****************************************************************************/

	class Results{

		private $category;		

		private $comparison_type;

		private $cat_id;
		private $entities;
		private $attributes;
		private $attribute_types;
		private $weights;
		private $choice;
		private $range;
		private $focus;
		private $distinct_individual_weights;
		private $distinct_individual_names;
		
		private $scores;
		

		private function fetch_arguments() {
			if( isset($_REQUEST['comparison_type']) ){
				$this->comparison_type = $_REQUEST['comparison_type'];
			}
		}
		public function Results( $category,$cat_id,$entities,$attributes,$attribute_types,$weights,$focus,$distinct_individual_weights,$distinct_individual_names,$choice,$range,$specific) {
			$this->category = $category;
			
			$this->cat_id = $cat_id;
			$this->entities = $entities;
			$this->attributes = $attributes;
			$this->attribute_types = $attribute_types;
			$this->weights = $weights;
			$this->distinct_individual_weights = $distinct_individual_weights;
			$this->distinct_individual_names = $distinct_individual_names;
			$this->choice = $choice;
			$this->focus = $focus;
			$this->range = $range;
			$this->specific = $specific;

			$this->fetch_arguments();


			if($this->comparison_type == "default") {
				$this->attributes = array();
				
				$tempAttributes = $this->category->get_attributes( 1 );
				//var_dump($tempAttributes);
				//die();
				
				//die();
				foreach( $tempAttributes as $index=>$attribute) {
					$this->attributes[] =  $attribute->id;
				}
				
			}




			$this->generate_results();

			


			

			//var_dump($this->cat_id);
			//var_dump($this->entities);
			//var_dump($this->attributes);
			//var_dump($this->attribute_types);
			//var_dump($this->weights);
			//var_dump($this->distinct_individual_weights);
			//var_dump($this->distinct_individual_names);
			
						

		}
		public function get_results() {
			return $this->scores;
		}

		private function generate_results(){
			$this->scores = array();
			$this->scores["entity_total_score"] = array();
			$this->scores["attribute_score"] = array();
			$this->scores["attribute_name"] = array();
			$this->scores["entities"] = array();

			foreach( $this->attributes as $key=>$attribute ) {
					$info = $this->category->get_specific_attribute( $attribute );
					$this->scores["attribute_name"][$attribute] = $info;
			}
			$entities_count = 0;
			foreach( $this->entities as $key=>$entity) {
				$entity_name = $this->category->get_specific_entity( $entity );
				$this->scores["entities"][$entity] = $entity_name;
				$entities_count++;
			}
			$this->scores["entities"]["count"] = $entities_count;
			
			
			
			
			if( $this->comparison_type == "custom" ){
				$this->compare_algorithm( $this->cat_id, $this->entities, $this->attributes, $this->weights, $this->distinct_individual_weights, 0, $this->focus, $this->choice, $this->range, $this->specific );
			}
			else if( $this->comparison_type == "default" ) {
				//echo "<script>console.log(".json_encode($this->attributes).");</script>";
				$this->compare_algorithm( $this->cat_id, $this->entities, $this->attributes, $this->weights, $this->distinct_individual_weights, 1, $this->focus, $this->choice, $this->range, $this->specific );
			}
			else echo "we are sorry, an error occured";

			//var_dump($this->scores);
		}
		private function compare_algorithm($cat_id, $ent_id, $attributes,$attributes_custom_weights,$attr_custom_weights, $is_default,$focus , $choice,$range,$specific){
		
			/*** CHECKING ARGUMENTS ***/


						

			/*
			echo "cat_id <br/>";			
			var_dump($cat_id);
			echo "entities <br/>";
			var_dump($ent_id);
			echo "attributes <br/>";
			var_dump($attributes);
			
			
			echo "attributes weights <br/>";
			var_dump($attributes_custom_weights);
			echo "attributes custom weights <br/>";
			var_dump($attr_custom_weights);
			echo "default <br/>";
			var_dump($is_default);
			echo "focus <br/>";
			var_dump($choice);
			echo "range <br/>";
			var_dump($range);
			echo "specific <br/>";
			var_dump($specific);
			echo "END OF CHECK";
			*/
			//die();
						
			//return;			
			

				$items = array();
        		$attributes_type = array();
        		$elements = array();
        		$attr_weights = array();
				$attributes_weights = array();
        		$items_attr_vals = array();
        		$counter = 0;
 
        		$attr_vals_all = array();
        		$type_elements = array();
				
				

        		/**edw thewrw oti exei epilexthei to default compare me vari diladi apo ti vasi
            		* ta opoia pairnw **/
				/*********************************DEFAULT COMPARE***************************************/	
        		if($is_default == 1){
				
						
						
            			foreach($attributes as $attr_id){
						
                			$attr_info = $this->category->get_specific_attribute($attr_id);
                			$attr_value_weight = array();
							
							/*vazw toys typous twn attributes ston pinaka attributes_type*/
							array_push($attributes_type,$attr_info->comparability);
							
							/*vazw ta vari pou dothikan ston pinaka attributes weights*/
							array_push($attributes_weights, $attr_info->default_weight);

                			if(intval($attr_info->comparability) == DISTINCT ){
									$attr_vals   = array();
                    				$type_elements = $attr_info->type_elements;
                
									/*pairnw ta preferences tis vasis gia kathe ksexwristi timi
									 * enos distinct attribute
									 */
                    				foreach($type_elements as $elem){
                        				array_push( $attr_vals          , $elem['Value']);
                        				array_push( $attr_value_weight  ,intval($elem['Preference']));

                     			}
                    				$attr_vals_all[$counter]   = $attr_vals;
                    				$attr_weights[$counter]    = $attr_value_weight;

                			}elseif(intval($attr_info->comparability) == COUNTABLE ){
                    				
									$elements = $attr_info->type_elements;
									/*pairnw ta default preferences gia ta countable attributes apo ti
									 * vasi
									 */
									if($elements["Comparison Type"] == BIG_IS_BETTER){ 
										$attr_weights[$counter]  = "higher";	
									}elseif($elements["Comparison Type"] == LOWER_IS_BETTER){ 
										$attr_weights[$counter]  = "lower";
									}elseif($elements["Comparison Type"] == MIDDLE_VALUE){
										$attr_weights[$counter]  = "average";
									}
                      			 

                			}

               			 $counter++;
            			}
				/*************************************CUSTOM COMPARE************************************/		
        		}elseif($is_default == 0){

						$counter = 0;
						$attributes_weights = $attributes_custom_weights;
 
            			foreach($attributes as $attr_id){

							$attr_info = $this->category->get_specific_attribute($attr_id);
							
							/*vazw toys typous twn attributes ston pinaka attributes_type*/
							array_push($attributes_type,$attr_info->comparability);
							$attr_value_weight = array();

							if(intval($attr_info->comparability) == DISTINCT ){
                    			$elements = $attr_info->type_elements;
								
                    			$attr_vals  = array();
        
                    			$i = 0;
								/*vazw gia kathe ksexwristo pithano value tou distinct
								 * attribute to varos pou dothike apo ton xristi
								 */
                    			foreach($elements as $elem){
                        			array_push( $attr_vals          , $elem['Value']);
                        			array_push( $attr_value_weight  , intval($attr_custom_weights[$counter][$i]));
									$i++;

                    			}
                    			$attr_vals_all[$counter]   = $attr_vals;
                    			$attr_weights[$counter]    = $attr_value_weight;
								
							}elseif(intval($attr_info->comparability) == COUNTABLE ){
								/*pairnw ton tropo sygrisis pou exei oristei apo ton pinaka choice*/
								if($choice[$counter] == "range"){
										$attr_weights[$counter]  = $choice[$counter];
								}elseif($choice[$counter] == "specific"){
										$attr_weights[$counter]  = $choice[$counter];
								}elseif( $choice[$counter] == "focus"){
										$attr_weights[$counter]  = $focus[$counter];
								
								}
								
							}
							$counter++;

						}
					}

			
				foreach($ent_id as $entity){
					$ent = $this->category->get_specific_entity(intval($entity));
         
					array_push($items, $entity);
				}
		
		

        /*sygentrwnw tis times pou exoune ta antikeimena gia kathe attribute
         * sto pinaka $items_attr_vals
         * kanontas diaxwrismo an einai countable i distinct
         */
        foreach($attributes as $attr_id){

            $attr_info = $this->category->get_specific_attribute($attr_id);
            // echo "attr id :".$attr_id;

            $attribute_values = array();

            if(intval($attr_info->comparability) == COUNTABLE ){
                foreach($ent_id as $entity){
                    
					$ent = $this->category->get_specific_entity($entity);
					array_push($attribute_values,intval($ent->entity_attribute_values[$attr_id]));
                }
                array_push($items_attr_vals,$attribute_values);

            }elseif(intval($attr_info->comparability) == DISTINCT ){
				
				foreach($ent_id as $entity){
                    $ent = $this->category->get_specific_entity($entity);
					array_push($attribute_values,$ent->entity_attribute_values[$attr_id]);

                }
                array_push($items_attr_vals,$attribute_values);

            }elseif(intval($attr_info->comparability) == UNCOMPARABLE ){
                array_push($items_attr_vals,$attribute_values);
            
            }

   
        }



        /* ypologizw to athroisma varwn olwn twn xaraktiristikwn */
        $sum =  array_sum($attributes_weights);
		
        $weights = array();
        $quant_attrs = array();
        $temp = array();
        $temp2 = array();
        $attribute_factor = array();
        $distances  = array();
        $MaxValue;
        $MinValue;


        /*ypologizw ta nea kanonikopoimena vari gia kathe xaraktiristiko
            * kai to vazw ston pinaka $weights
        */
        foreach ($attributes_weights as &$value) {
			array_push($weights, $value/$sum);
        }

        /*gia kathe xaraktiristiko*/
        for($i = 0; $i < count($attributes); ++$i)
        {
            /*an to xaraktirisiko einai poiotiko*/
            if($attributes_type[$i] == DISTINCT){

                /* ypologizw to athroisma varw gia kathe
                 * diakriti timi
                 */
				$sum =  array_sum($attr_weights[$i]);
				$temp = $attr_weights[$i];

                /*ypologizw to neo kanonikopoihmeno varos
                 * gia kathe timi tou xaraktiristikou
                 */
				
				if($sum!=0){
					foreach ($temp as &$value) {
						$value = $value / $sum;
					}
				}
				/*k to ksanavaw ston arxiko pinaka*/
				$attr_weights[$i] = $temp;
				

                /*ypologizw ta attribute factors*/
                for($j = 0; $j < count($items) ; ++$j){
                       for($k =0; $k < count($attr_vals_all[$i]) ; ++$k){
					   
							
							if(!empty($items_attr_vals[$i][$j])){
								if($items_attr_vals[$i][$j] == $attr_vals_all[$i][$k]){
								//echo "ITEM : ".$items[$j]." has value: ".$items_attr_vals[$i][$j]." for attribute ".$attributes[$i]." and takes factor: ".$attr_weights[$i][$k]."</br>";
										$attribute_factor[$j][$i] = $attr_weights[$i][$k];
								
								
								}
							}else{
								/*an to antikeimeno gia ayto to attr exei null timi
								 * tou dinw factor arnitiko
								 */
								$attribute_factor[$j][$i] = -1;
							}
                       }
                }
            }

            /*an to xaraktiristiko einai posotiko*/
            elseif($attributes_type[$i] == COUNTABLE){

				$temp_array = array();
				$temp3 = array();
                $Distances  = array();

                /*vazw sto temp array tis times twn antikeimenwn
                 * gia ayto to xaraktiristiko kai tis taksinomw
                 */
				$temp_array = $items_attr_vals[$i];
				asort($temp_array);

                /* tropos sygrisis*/
				$temp = $attr_weights[$i];

			/*---------------------------------------MAXIMUM--------------------------------------------------*/
			/*-an o tropos sygrisis pou dothike einai "megisti timi"-*/
		
				if($attr_weights[$i]=="higher"){
                    
					
										
					/*pairnw tin megisti timi tou pinaka*/
                    $MaxValue = end($temp_array);

                     /*gia kathe antikeimeno ypologizw tin apostasi tis timis tou*/
                    for($j= 0; $j <  count($items); ++$j){
						if(!empty($items_attr_vals[$i][$j])){
							$distances[$i][$j] = abs($MaxValue - $items_attr_vals[$i][$j]);
						
						}else{
							
							$distances[$i][$j] = -1;
						}
                                             
                    }

                    	$temp_array = $distances[$i];
						$temp_ar2 = $distances[$i];

						asort($temp_array);
						arsort($temp_ar2);
				   			
						$Max_Dist = reset($temp_ar2)+0.5;
                              
                    
                    /*thetw k pali ton attribute factor*/
                    for($j = 0; $j < count($items); ++$j){

                        /*an i timi tou $item[$j] sybeftei me tin megisti
                         * h exei tin elaxisti apostasi apo aytin , dwse
                         * paragonta 1(max factor)
                         */
                        if($distances[$i][$j]==0){
                                $attribute_factor[$j][$i] = 1;
                                

                        }else{
								if($distances[$i][$j] != -1){
									$attribute_factor[$j][$i] = ($Max_Dist-$distances[$i][$j])/$Max_Dist;
								}else{
									/*an to antikeimeno gia ayto to attr exei null timi
									* tou dinw factor arnitiko
									*/
									$attribute_factor[$j][$i] = -1;
								}
                                 
                                
                        }
                    }
		
		/*----------------------------------------MINIMUM------------------------------------------------*/
				}elseif($attr_weights[$i]=="lower"){

					/*pairnw tin megisti timi tou pinaka*/
                    $MinValue = reset($temp_array);
                    
                     /*gia kathe antikeimeno ypologizw tin apostasi tis timis tou*/
                    for($j= 0; $j <  count($items); ++$j){
						if(!empty($items_attr_vals[$i][$j])){
							$distances[$i][$j] = abs($items_attr_vals[$i][$j]- $MinValue);
						}else{
							$distances[$i][$j] = -1;
						}
                        

                    }

                    $temp_array = $distances[$i];
					$temp_ar2 = $distances[$i];

					asort($temp_array);
					arsort($temp_ar2);
				   
					
					$Max_Dist = reset($temp_ar2)+0.5;
					
                    
                    /*thetw k pali ton attribute factor*/
                    for($j = 0; $j < count($items); ++$j){

                        /*an i timi tou $item[$j] sybeftei me tin megisti
                         * h exei tin elaxisti apostasi apo aytin , dwse
                         * paragonta 1(max factor)
                         */
                        if($distances[$i][$j]==0){
                                $attribute_factor[$j][$i] = 1;

                        }else{
								if($distances[$i][$j] != -1){
									$attribute_factor[$j][$i] = ($Max_Dist-$distances[$i][$j])/$Max_Dist;
								}else{
									/*an to antikeimeno gia ayto to attr exei null timi
									* tou dinw factor arnitiko
									*/
									$attribute_factor[$j][$i] = -1;
								}

                        }
                    }

				/*---------------------------------MEAN VALUE------------------------------------*/
				}elseif($attr_weights[$i]=="average"){

//echo "</br>";
					/*pairnw tin megisti timi tou pinaka*/
					$MeanValue = array_sum($temp_array)/count($temp_array);

					//echo "MEAN VALUE : ".$MeanValue."</br>";
					/*gia kathe antikeimeno ypologizw tin apostasi tis timis tou*/
					for($j= 0; $j <  count($items); ++$j){
						if(!empty($items_attr_vals[$i][$j])){
							$distances[$i][$j] = abs($items_attr_vals[$i][$j]- $MeanValue);
							//echo "ITEM ".$items[$j]." HAS VALUE ".$items_attr_vals[$i][$j]." and distance ".$distances[$i][$j]."</br>";
						}else{
							$distances[$i][$j] = -1;
						}
					}

					$temp_array = $distances[$i];
					$temp_ar2 = $distances[$i];

					asort($temp_array);
					arsort($temp_ar2);
				   
					
					$Max_Dist = reset($temp_ar2)+0.5;

					
                    /*thetw k pali ton attribute factor*/
                    for($j = 0; $j < count($items); ++$j){

                        /*an i timi tou $item[$j] sybeftei me tin megisti
                         * h exei tin elaxisti apostasi apo aytin , dwse
                         * paragonta 1(max factor)
                         */
                        if($distances[$i][$j]==0){
                                $attribute_factor[$j][$i] = 1;

                        }else{
								if($distances[$i][$j] !=-1){
									$attribute_factor[$j][$i] = ($Max_Dist-$distances[$i][$j])/$Max_Dist;
								}else{
									/*an to antikeimeno gia ayto to attr exei null timi
									* tou dinw factor arnitiko
									*/
									$attribute_factor[$j][$i] = -1;
								
								}

                        }
                    }
				/*--------------------------------SPECIFIC VALUE--------------------------------------*/
				}elseif($attr_weights[$i]=="specific"){


				/*pairnw tin megisti timi tou pinaka*/
                $WantedValue = floatval($specific[$i]);

                /*gia kathe antikeimeno ypologizw tin apostasi tis timis tou*/
                for($j= 0; $j <  count($items); ++$j){
					if(!empty($items_attr_vals[$i][$j])){
						$distances[$i][$j] = abs($items_attr_vals[$i][$j]- $WantedValue);
					}else{
						$distances[$i][$j] =-1;
					}
				}

					$temp_array = $distances[$i];
					$temp_ar2 = $distances[$i];

					asort($temp_array);
					arsort($temp_ar2);
				   
					
					$Max_Dist = reset($temp_ar2)+0.5;

                 /* prepei na thesw alli mia elaxisti apostasi !=0
                  * gia na mporw na kanw ypologismous
                  */

                  foreach($temp_array as $dist){
                        if($dist != 0){
                                $MinDistance = $dist;
                                break;

                            }
                        }


                    /*thetw k pali ton attribute factor*/
                    for($j = 0; $j < count($items); ++$j){

                        /*an i timi tou $item[$j] sybeftei me tin megisti
                         * h exei tin elaxisti apostasi apo aytin , dwse
                         * paragonta 1(max factor)
                         */
                        if($distances[$i][$j]==0){
                                $attribute_factor[$j][$i] = 1;

                        }else{
								if($distances[$i][$j] != -1){
									$attribute_factor[$j][$i] = ($Max_Dist-$distances[$i][$j])/$Max_Dist;
								}else{
									/*an to antikeimeno gia ayto to attr exei null timi
									* tou dinw factor arnitiko
									*/
									$attribute_factor[$j][$i] = -1;
								}

                        }
                    }
			/*----------------------------------WIDTH OF VALUES---------------------------------------*/
            }elseif($attr_weights[$i]=="range"){


				/*pairnw ta akra tou diastimatos*/
                $Max = floatval($range[$i]['max']);
                $Min = floatval($range[$i]['min']);

                 for($j= 0; $j <  count($items); ++$j){

				 if(!empty($items_attr_vals[$i][$j] )){
						if(($items_attr_vals[$i][$j] <= $Max) && ($items_attr_vals[$i][$j]>= $Min )){

							$distances[$i][$j] = 0;

						}elseif($items_attr_vals[$i][$j] > $Max){
							$distances[$i][$j] = abs($items_attr_vals[$i][$j] - $Max);


						}elseif($items_attr_vals[$i][$j] < $Min){
							$distances[$i][$j] = abs($items_attr_vals[$i][$j] - $Min);

						}
				}else{
						$distances[$i][$j] = -1;
				}

                 }

					/*taksinomw ton pinaka twn apostasewn*/
					$temp_array = $distances[$i];
					$temp_ar2 = $distances[$i];

					asort($temp_array);
					arsort($temp_ar2);
				   
					$Max_Dist = reset($temp_ar2)+0.5;

                 


                    /*thetw k pali ton attribute factor*/
                    for($j = 0; $j < count($items); ++$j){

                        /*an i timi tou $item[$j] sybeftei me tin megisti
                         * h exei tin elaxisti apostasi apo aytin , dwse
                         * paragonta 1(max factor)
                         */
                        if($distances[$i][$j]==0){

                           $attribute_factor[$j][$i] = 1;

                        }else{
								if($distances[$i][$j] != -1){
									$attribute_factor[$j][$i] = ($Max_Dist-$distances[$i][$j])/$Max_Dist;
								}else{
									/*an to antikeimeno gia ayto to attr exei null timi
									* tou dinw factor arnitiko
									*/
									$attribute_factor[$j][$i] =-1;
								}

                        }
                    }
            }

	}elseif($attributes_type[$i] == UNCOMPARABLE){
		/*do nothing*/
            
        }
    }


    $attr_score = array();
		//echo "</br></br>";

	
    foreach($items as $key_item=>$val){
		$this->scores["attribute_score"][$val] = array();
		
        foreach($attributes as $key => $value){
		
            if($attributes_type[$key] != UNCOMPARABLE){
            
				
				if($attribute_factor[$key_item][$key]!=-1){
				
					$attr_score[$key_item][$key] = $attributes_weights[$key]*$attribute_factor[$key_item][$key];
					$this->scores["attribute_score"][$val][$key] = $attr_score[$key_item][$key];
					//echo "Item ".$items[$key_item]." takes score for attribute ".$attributes[$key]." :".$attr_score[$key_item][$key]."</br>";  
					
				}else{
				
					/*an o factor pou dothike einai -1 dinw score 0*/
					$this->scores["attribute_score"][$val][$key] = 0;
					$attr_score[$key_item][$key] = 0;
				}
            }

        }
      //  echo "~~~~~~~~~~~~~~~~~~~~~~</br>";
    }

	//var_dump($this->scores["entity_total_score"]);
	//echo "<script>console.log(\"attr_score\"".json_encode($attr_score).");</script>";
	//echo "<script>console.log(\"after\");</script>";
    for($i = 0; $i < count($items); ++$i){
			
			$this->scores["entity_total_score"][$items[$i]] = array_sum($attr_score[$i]);
        
    }

		//asort($this->scores["entity_total_score"]);
		//die();
		
}
			        

	
	}


?>
