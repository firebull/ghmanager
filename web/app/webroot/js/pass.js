// Script is taken at http://www.intelligent-web.co.uk/tutorial.jsp?tutorialID=password_validation.html 
// Thanks to creator!
//


var commonPasswords = new Array('password', 'pass', '1234', '1246'); 
 
var numbers = "0123456789"; 
var lowercase = "abcdefghijklmnopqrstuvwxyz"; 
var uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
var punctuation = "!.@$£#*()%~<>{}[]"; 
 
function checkPassword(password) { 
 
    var combinations = 0; 
 
    if (contains(password, numbers) > 0) { 
        combinations += 10; 
    } 
 
    if (contains(password, lowercase) > 0) { 
        combinations += 26; 
    } 
 
    if (contains(password, uppercase) > 0) { 
        combinations += 26; 
    } 
 
    if (contains(password, punctuation) > 0) { 
        combinations += punctuation.length; 
    } 
 
    // work out the total combinations 
    var totalCombinations = Math.pow(combinations, password.length); 
 
    // if the password is a common password, then everthing changes... 
    if (isCommonPassword(password)) { 
        totalCombinations = 75000 // about the size of the dictionary 
    } 
 
    // work out how long it would take to crack this (@ 200 attempts per second) 
    var timeInSeconds = (totalCombinations / 200) / 2; 
 
    // this is how many days? (there are 86,400 seconds in a day. 
    var timeInDays = timeInSeconds / 86400 
 
    // how long we want it to last 
    var lifetime = 365; 
 
    // how close is the time to the projected time? 
    var percentage = timeInDays / lifetime; 
 
    var friendlyPercentage = cap(Math.round(percentage * 100), 100); 
    if (totalCombinations != 75000 && friendlyPercentage < (password.length * 5)) { 
        friendlyPercentage += password.length * 5; 
    } 
 
    var progressBar = document.getElementById("progressBar"); 
    //progressBar.style.width = friendlyPercentage + "%"; 
 
    if (percentage > 1) { 
        // strong password 
        progressBar.style.backgroundColor = "#3bce08"; 
        return; 
    } 
 
    if (percentage > 0.5) { 
        // reasonable password 
        progressBar.style.backgroundColor = "#ffd801"; 
        return; 
    } 
 
    if (percentage > 0.10) { 
        // weak password 
        progressBar.style.backgroundColor = "orange"; 
        return; 
    } 
 
    // useless password! 
    if (percentage <= 0.10) { 
        // weak password 
        progressBar.style.backgroundColor = "red"; 
        return; 
    } 
 
 
} 
 
function cap(number, max) { 
    if (number > max) { 
        return max; 
    } else { 
        return number; 
    } 
} 
 
function isCommonPassword(password) { 
 
    for (i = 0; i < commonPasswords.length; i++) { 
        var commonPassword = commonPasswords[i]; 
        if (password == commonPassword) { 
            return true; 
        } 
    } 
 
    return false; 
 
} 
 
function contains(password, validChars) { 
 
    count = 0; 
 
    for (i = 0; i < password.length; i++) { 
        var char = password.charAt(i); 
        if (validChars.indexOf(char) > -1) { 
            count++; 
        } 
    } 
 
    return count; 
} 

/*==================================================*
$Id: verifynotify.js,v 1.1 2003/09/18 02:48:36 pat Exp $
Copyright 2003 Patrick Fitzgerald
http://www.barelyfitz.com/webdesign/articles/verify-notify/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*==================================================*/


function verifynotify(field1, field2, result_id) {
this.field1 = field1;
this.field2 = field2;
this.result_id = result_id;
this.match_html = '<SPAN STYLE=\"color:green\">пароли совпадают<\/SPAN>';
this.nomatch_html = '<SPAN STYLE=\"color:red\">пароли не совпадают<\/SPAN>';

	this.check = function() {
	
	  // Make sure we don't cause an error
	  // for browsers that do not support getElementById
	  if (!this.result_id) { return false; }
	  if (!document.getElementById){ return false; }
	  r = document.getElementById(this.result_id);
	  if (!r){ return false; }
	
	  if (this.field1.value != "" && this.field1.value == this.field2.value) {
		//Поле не пустое, выводить текстом и пароли совпадают  
	    r.innerHTML = this.match_html;
	    document.getElementById("progressSubmit").disabled=false;
	    return true;
	  } else if (this.field1.value != "" && this.field1.value != this.field2.value){
		//Поле не пустое, выводить текстом и пароли НЕ совпадают  
	    r.innerHTML = this.nomatch_html;
	    document.getElementById("progressSubmit").disabled=true;
	    return false;
	  }  else if (this.field1.value == "" && this.field2.value== ""){
			//Поле пустое  
		    r.innerHTML = '';
		    document.getElementById("progressSubmit").disabled=true;
		    return false;
	  }
	  
	}
}

