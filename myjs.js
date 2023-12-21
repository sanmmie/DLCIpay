var ajx;
var ajxbool;

try{
	ajx=new XMLHttpRequest();
	ajxbool=true;
} catch(e){
	ajx=new ActiveXObject("Microsoft.XMLHTTP");
	ajxbool=true;
}

document.getElementById('gobill').value='no';
function gocustomer(a,b){
	var did;
	
	did='cust-load';
	if(b==''){
		document.getElementById('gobill').value='no';	
		document.getElementById(did).style.display='none';
		exit;
	}
	
	confirmed = 0;
	document.getElementById(did).style.display='block';
	document.getElementById(did).innerHTML="checking...";
	if (ajxbool==true){		
		ajx.onreadystatechange=function(){
			if(ajx.readyState==4){	
				//slert("CXX");
				var resp=ajx.responseText;
				
				if(resp=="failed"){
					confirmed = 0;
					document.getElementById(did).innerHTML="Unable to load customer details!";
				}
				else if(resp==""){
					confirmed = 0;	
					document.getElementById(did).innerHTML="Connection error!";	
				}
				else{
					if(a=='gotv' || a=='dstv'){
						var res = resp.split("|");
						var info=res[0];
						
						confirmed = 1;
						document.getElementById('goname').value=res[1];
						document.getElementById('goinvoice').value=res[2];
						document.getElementById('gocustno').value=res[3];
						
						document.getElementById(did).innerHTML=info;
						document.getElementById('gobill').value='yes';
					}
					else{
						confirmed = 1;
						document.getElementById(did).innerHTML=resp;
						document.getElementById('gobill').value='yes';
					}
					
				}
			}
		}
		var code="?bill=" + a + "&smartno="+b ;
		var pga="billcheck.php" + code;
		ajx.open ("GET",pga,true);
		ajx.send(null);
	}	
	else{
		alert('Not connected!');
	}
}

function restme(){
	did='cust-load';	
	document.getElementById(did).innerHTML="";
}