// JavaScript Document

function confirmStatus(iStatus){
	
		if(iStatus == 1)
		{
			var check = confirm('Do you want to continue to make this user active?');
			if(check)
			{
				return true;
			}else{
				return false;		
			}
		}else if(iStatus == 2){
			var check = confirm('Do you want to continue to make this user de-active?');
			if(check)
			{
				return true;
			}else{
				return false;		
			}
		}else{
			return false;	
		}
}


function confirmDelete(){
		var check = confirm('Do you want to continue to delete this?');
			if(check)
			{
				return true;
			}else{
				return false;		
			}
}

function showAccessDiv(id){
	if(id == 1){
		document.getElementById('divAccess').style.display = 'none';	
	}else if(id == 2){
		document.getElementById('divAccess').style.display = 'block';	
	}
}