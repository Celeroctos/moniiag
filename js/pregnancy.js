$("document").ready(function(){
	inputs=$("input")
	for (var i = 0; i < inputs.length; i++) {
	   (function(i) {
		  inputs[i].onchange = function() {
			switch(inputs[i].id){
			case "f__2-500-1_1836":
				dateArr=inputs[i].value.split("-");
				inputDate= new Date(dateArr[0],dateArr[1]-1,dateArr[2]);
				nowDate=new Date();
				if (inputDate<=nowDate){
					inputs[i+1].style.backgroundColor="";
					inputs[i+3].style.backgroundColor="";
					inputs[i+2].style.backgroundColor="";
					temp = nowDate.valueOf()-inputDate.valueOf();
					inputs[i+4].value = parseInt(temp/604800000)+" нед. "+parseInt(temp%604800000/86400000)+" дней";
					temp = new Date(inputDate.valueOf()+280*86400000);
					preDate= "";
					if (temp.getDate()<10){ preDate+="0"+temp.getDate();}
					else {preDate+=temp.getDate();};
					if ((temp.getMonth()+1)<10){ preDate+=".0"+(temp.getMonth()+1);}
					else {preDate+="."+(temp.getMonth()+1);}
					preDate+="."+temp.getFullYear();
					inputs[i+5].value = preDate;
				}
				else{
					inputs[i+1].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+3].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+2].style.backgroundColor="rgb(255, 196, 196)";
				}
				break
			case "f__2-500-20_1853": case"f__2-500-23_1856": 
			case "f__2-500-26_1859":
				dateArr=inputs[i].value.split("-");
				inputDate= new Date(dateArr[0],dateArr[1]-1,dateArr[2]);
				nowDate=new Date();
				if (inputDate<=nowDate){
					inputs[i+1].style.backgroundColor="";
					inputs[i+3].style.backgroundColor="";
					inputs[i+2].style.backgroundColor="";
					temp = nowDate.valueOf()-inputDate.valueOf();
					inputs[i+4].value = parseInt(temp/604800000)+" нед. "+parseInt(temp%604800000/86400000)+" дней";
					temp = new Date(inputDate.valueOf()+266*86400000);
					preDate= "";
					if (temp.getDate()<10){ preDate+="0"+temp.getDate();}
					else {preDate+=temp.getDate();};
					if ((temp.getMonth()+1)<10){ preDate+=".0"+(temp.getMonth()+1);}
					else {preDate+="."+(temp.getMonth()+1);}
					preDate+="."+temp.getFullYear();
					inputs[i+5].value = preDate;
				}
				else{
					inputs[i+1].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+3].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+2].style.backgroundColor="rgb(255, 196, 196)";
				}
				break
			case "f__2-500-30_1862": case "f__2-500-9_1844":
				dateArr=inputs[i].value.split("-");
				inputDate= new Date(dateArr[0],dateArr[1]-1,dateArr[2]);
				nowDate=new Date();
				if (inputDate<=nowDate){
					inputs[i+1].style.backgroundColor="";
					inputs[i+3].style.backgroundColor="";
					inputs[i+2].style.backgroundColor="";
					calc(inputs,i,2);
				}
				else{
					inputs[i+1].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+3].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+2].style.backgroundColor="rgb(255, 196, 196)";
				}
				break
				case "f__2-500-4_1839": 
				dateArr=inputs[i].value.split("-");
				inputDate= new Date(dateArr[0],dateArr[1]-1,dateArr[2]);
				nowDate=new Date();
				if (inputDate<=nowDate){
					inputs[i+1].style.backgroundColor="";
					inputs[i+3].style.backgroundColor="";
					inputs[i+2].style.backgroundColor="";
					calc(inputs,i);
				}
				else{
					inputs[i+1].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+3].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+2].style.backgroundColor="rgb(255, 196, 196)";
				}
				break
			case "f__2-500-15_1849":
				dateArr=inputs[i].value.split("-");
				inputDate= new Date(dateArr[0],dateArr[1]-1,dateArr[2]);
				nowDate=new Date();
				if (inputDate<=nowDate){
					inputs[i+1].style.backgroundColor="";
					inputs[i+3].style.backgroundColor="";
					inputs[i+2].style.backgroundColor="";
					if ($("#f__2-500-15_1849-cont")[0].parentNode.childNodes[11].options[$("#f__2-500-15_1849-cont")[0].parentNode.childNodes[11].selectedIndex].value == 2330){
						temp = nowDate.valueOf()-inputDate.valueOf()+140*86400000;
						inputs[i+4].value = parseInt(temp/604800000)+" нед. "+parseInt(temp%604800000/86400000)+" дней";
						temp = new Date(inputDate.valueOf()+140*86400000);
						preDate= "";
						if (temp.getDate()<10){ preDate+="0"+temp.getDate();}
						else {preDate+=temp.getDate();};
						if ((temp.getMonth()+1)<10){ preDate+=".0"+(temp.getMonth()+1);}
						else {preDate+="."+(temp.getMonth()+1);}
						preDate+="."+temp.getFullYear();
						inputs[i+5].value = preDate;
					}
					else if ($("#f__2-500-15_1849-cont")[0].parentNode.childNodes[11].options[$("#f__2-500-15_1849-cont")[0].parentNode.childNodes[11].selectedIndex].value == 2331){
						temp = nowDate.valueOf()-inputDate.valueOf()+126*86400000;
						inputs[i+4].value = parseInt(temp/604800000)+" нед. "+parseInt(temp%604800000/86400000)+" дней";
						temp = new Date(inputDate.valueOf()+126*86400000);
						preDate= "";
						if (temp.getDate()<10){ preDate+="0"+temp.getDate();}
						else {preDate+=temp.getDate();};
						if ((temp.getMonth()+1)<10){ preDate+=".0"+(temp.getMonth()+1);}
						else {preDate+="."+(temp.getMonth()+1);}
						preDate+="."+temp.getFullYear();
						inputs[i+5].value = preDate;
					}
				}
				else{
					inputs[i+1].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+3].style.backgroundColor="rgb(255, 196, 196)";
					inputs[i+2].style.backgroundColor="rgb(255, 196, 196)";
				}
				break
			case "f__2|500|31_1863":
					calc(inputs,i-4,2);
					break
			case "f__2|500|5_1840":
					calc(inputs,i-4,1);
					break
			case "f__2|500|10_1845":
					calc(inputs,i-4,2);
					break
			case "f__2|500|33_1864": 
					calc(inputs,i-5,2);
					break
			case "f__2|500|11_1846":
					calc(inputs,i-5,2);
					break
			case "f__2|500|6_1841": 
					calc(inputs,i-5,1);
					break
		  }
		  }
	   })(i);
	}
});
function calc (inputs,i,cof){
	dateArr=inputs[i].value.split("-");
	inputDate= new Date(dateArr[0],dateArr[1]-1,dateArr[2]);
	nowDate=new Date();
	if ((inputs[i].value!="")&&(inputs[i+4].value!="")&&(inputs[i+5].value!="")&&(nowDate>=inputDate)){
		
		temp = nowDate.valueOf()-inputDate.valueOf()+inputs[i+4].value*604800000+inputs[i+5].value*86400000;
		inputs[i+6].value = parseInt(temp/604800000)+" нед. "+parseInt(temp%604800000/86400000)+" дней";
		if (cof==2) {
		temp = new Date(inputDate.valueOf()+280*86400000-inputs[i+4].value*604800000-inputs[i+5].value*86400000);
		preDate= "";
		if (temp.getDate()<10){ preDate+="0"+temp.getDate();}
		else {preDate+=temp.getDate();};
		if ((temp.getMonth()+1)<10){ preDate+=".0"+(temp.getMonth()+1);}
		else {preDate+="."+(temp.getMonth()+1);}
		preDate+="."+temp.getFullYear();
		inputs[i+7].value = preDate;}
	}
}
$("document").ready(function(){
	selects=$("select")
	for (var i = 0; i < selects.length; i++) {
	   (function(i) {
			selects[i].onchange = function() {
				if (selects[i].options[selects[i].selectedIndex].value == 2330){
					if ($("#f__2-500-15_1849")[0].value!=""){	
						dateArr=$("#f__2-500-15_1849")[0].value.split("-");
						inputDate= new Date(dateArr[0],dateArr[1]-1,dateArr[2]);
						nowDate=new Date();
						if (inputDate<=nowDate){
							temp = nowDate.valueOf()-inputDate.valueOf()+140*86400000;
							$("#f__2-500-15_1849-cont")[0].parentNode.childNodes[17].value = parseInt(temp/604800000)+" нед. "+parseInt(temp%604800000/86400000)+" дней";
							temp = new Date(inputDate.valueOf()+140*86400000);
							preDate= "";
							if (temp.getDate()<10){ preDate+="0"+temp.getDate();}
							else {preDate+=temp.getDate();};
							if ((temp.getMonth()+1)<10){ preDate+=".0"+(temp.getMonth()+1);}
							else {preDate+="."+(temp.getMonth()+1);}
							preDate+="."+temp.getFullYear();
							$("#f__2-500-15_1849-cont")[0].parentNode.childNodes[23].value = preDate;
						}
					}	
				}
				else if (selects[i].options[selects[i].selectedIndex].value == 2331){
					if ($("#f__2-500-15_1849")[0].value!=""){	
						dateArr=$("#f__2-500-15_1849")[0].value.split("-");
						inputDate= new Date(dateArr[0],dateArr[1]-1,dateArr[2]);
						nowDate=new Date();
						if (inputDate<=nowDate){
							temp = nowDate.valueOf()-inputDate.valueOf()+140*86400000;
							$("#f__2-500-15_1849-cont")[0].parentNode.childNodes[17].value = parseInt(temp/604800000)+" нед. "+parseInt(temp%604800000/86400000)+" дней";
							temp = new Date(inputDate.valueOf()+140*86400000);
							preDate= "";
							if (temp.getDate()<10){ preDate+="0"+temp.getDate();}
							else {preDate+=temp.getDate();};
							if ((temp.getMonth()+1)<10){ preDate+=".0"+(temp.getMonth()+1);}
							else {preDate+="."+(temp.getMonth()+1);}
							preDate+="."+temp.getFullYear();
							$("#f__2-500-15_1849-cont")[0].parentNode.childNodes[23].value = preDate;
						}
					}	
				}
			}
		})(i);
	}
});