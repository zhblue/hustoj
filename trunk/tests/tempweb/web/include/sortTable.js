         //table sort from http://dennis-zane.javaeye.com/blog/58864
         //类型转换器，将列的字段类型转换为可以排序的类型：String,int,float
            function convert(sValue, sDataType) {
                switch(sDataType) {
                    case "int":
                        return parseInt(sValue);
                    case "float":
                        return parseFloat(sValue);
                    case "date":
                        return new Date(Date.parse(sValue));
                    default:
                        return sValue.toString();
                
                }
            }
            
            //排序函数产生器，iCol表示列索引，sDataType表示该列的数据类型
            function generateCompareTRs(iCol, sDataType) {
        
                return  function compareTRs(oTR1, oTR2) {
					        var td1=oTR1.cells[iCol].firstChild;
					        var td2=oTR2.cells[iCol].firstChild;
					        
							td1=td1.innerText || td1.textContent;
							td2=td2.innerText || td2.textContent;
							

                            var vValue1 = convert(td1, sDataType);
                            var vValue2 = convert(td2, sDataType);
        
                            if (vValue1 < vValue2) {
                                return -1;
                            } else if (vValue1 > vValue2) {
                                return 1;
                            } else {
                                return 0;
                            }
                        };
            }
            
            //排序方法
            function sortTable(sTableID, iCol, sDataType) {
                var oTable = document.getElementById(sTableID);
                var oTBody = oTable.tBodies[0];
                var colDataRows = oTBody.rows;
                var aTRs = new Array;
                
                //将所有列放入数组
                for (var i=0; i < colDataRows.length; i++) {
                    aTRs[i] = colDataRows[i];
                }
                 
                //判断最后一次排序的列是否与现在要进行排序的列相同，是的话，直接使用reverse()逆序
                if (oTable.sortCol == iCol) {
                    aTRs.reverse();
                } else {
                    //使用数组的sort方法，传进排序函数
                    aTRs.sort(generateCompareTRs(iCol, sDataType));
                }
        
                var oFragment = document.createDocumentFragment();
                for (var i=0; i < aTRs.length; i++) {
					if(i%2==0)
						aTRs[i].className='evenrow';
					else
						aTRs[i].className='oddrow';
                    oFragment.appendChild(aTRs[i]);
                }
       
                oTBody.appendChild(oFragment);
                //记录最后一次排序的列索引
                oTable.sortCol = iCol;
            }
