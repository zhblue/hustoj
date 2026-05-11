function Pie(_div)
{
	var piejg = new jsGraphics(_div);
	var colors = new Array(); 
	colors[9] = "#0066FF";
	colors[5] = "#996633";
	colors[2] = "#80bb80";
	colors[3] = "#FF0066";
	colors[4] = "#9900FF";
	colors[6] = "#006633";
	colors[1] = "#8080FF";
	colors[7] = "#000000";
	colors[8] ="#CCFFFF";
	colors[0] = "#FF8080";
	colors[10] = "#066600";
	colors[11] ="#666666";
	
	this.start_x = 0;
	this.start_y = 0;
	this.width= 100;
	this.height= 100;
	this.desc_distance = 80;
	this.desc_width = 10;
	this.desc_height= 10;
	this.IsShowPercentage =true;
	this.IsShowShadow =true;
	this.IsDescRight=true;
	this.nextRow = 2;
	
	this.drawPie =function (y_value,x_value)
	{
		if(this.IsShowShadow)
		{
			piejg.setColor("#666666");
			piejg.fillEllipse(this.start_x+5, this.start_y+5, this.width,	this.height);
      piejg.setColor("#CCFFFF");
			piejg.fillEllipse(this.start_x, this.start_y, this.width,	this.height);
		}
		var Percentage = new Array();
		var y_len = y_value.length;
		var x_len = x_value.length;
		var sum = 0;
		var perspective  = new Array();
		var begin_perspective = 0;
		var end_perspective = 0;
		
		if(y_len != x_len)
		{
			alert("X and Y length of inconsistencies, errors parameters.");
			return;
		}
		for(var i = 0; i<y_len;i++)
		{
			sum+=y_value[i];
		}
		for (var i = 0; i<y_len;i++)
		{
			if(isNaN(y_value[i]))
			{
				alert("y is not a number!");
				return;
			}
			perspective[i] = Math.max(Math.round(360*y_value[i]/sum),1);
			Percentage[i] =Math.round(100*y_value[i]/sum);
			end_perspective +=perspective[i];
			if(i==0)
			{
				piejg.setColor(colors[i]); 
				piejg.fillArc(this.start_x,this.start_y,this.width,this.height, 0, end_perspective); 
			}
			else
			{	
				begin_perspective += perspective[i-1];
				piejg.setColor(colors[i]); 
				piejg.fillArc(this.start_x,this.start_y,this.width,this.height, begin_perspective, end_perspective); 
			}
			
		}
		var temp_x = 0;
		var temp_y = 0;
		if(this.IsDescRight)
		{
			for(var i = 0 ;i<x_len;i++)
			{
				temp_x = this.width+10+this.start_y;
				temp_y = this.start_y+(i-x_len/2+1/2)*(this.height/x_len)+this.height/2;
				//temp_y = this.start_y+(i+1)*(this.height/x_len);
				piejg.setColor(colors[i]);
				piejg.fillRect(temp_x,temp_y,this.desc_width,this.desc_height);  
				if(this.IsShowPercentage)
				{
					piejg.drawString(x_value[i]+"["+Percentage[i]+"%]",temp_x+this.desc_width,temp_y); 
				}else
				{
					piejg.drawString(x_value[i],temp_x+this.desc_width,temp_y); 
				}		
			}
		}
		else
		{
			for(var i = 0 ;i<x_len;i++)
			{
				temp_x = i*this.desc_distance+this.start_x;
				temp_y = this.height+10+this.start_y;
				if(i-this.nextRow>=0)
				{
					temp_x = (i-this.nextRow)*this.desc_distance+this.start_x;
					temp_y=this.height+10+30+this.start_y;
					
				}
				if(i-this.nextRow*2>=0)
				{
					temp_x = (i-this.nextRow*2)*this.desc_distance+this.start_x;
					temp_y=this.height+10+60+this.start_y;
					
				}
					if(i-this.nextRow*3>=0)
				{
					temp_x = (i-this.nextRow*3)*this.desc_distance+this.start_x;
					temp_y=this.height+10+90+this.start_y;
					
				}
				piejg.setColor(colors[i]);
				piejg.fillRect(temp_x,temp_y,this.desc_width,this.desc_height);  
				if(this.IsShowPercentage)
				{
					piejg.drawString(x_value[i]+"["+Percentage[i]+"%]",this.desc_width+3+temp_x,temp_y); 
				}else
				{
					piejg.drawString(x_value[i],this.desc_width+3+temp_x,temp_y); 
				}		
			}
		}
		piejg.paint();
	
	};
	this.clearPie= function()
	{
		piejg.clear();
	};
}