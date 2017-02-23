/**
 * 
 */
var i = 0; /* Set Global Variable i */
function increment(){
	i += 1; //Increment counter
}

/* Function to add new stock product entry. */
//********* KM *********
function addStkEntry()
{
increment();

var tr = document.createElement('tr');

var td1 = document.createElement('td');
var pn = document.createElement("INPUT");
pn.setAttribute("type", "text");
//txt.setAttribute("placeholder", "Product Name");
pn.setAttribute("Name", "sProdName" + i);
pn.setAttribute("id", "sProdName" + i);
pn.setAttribute("size", "16");
td1.appendChild(pn);

var pi = document.createElement("INPUT");
pi.setAttribute("type", "text");
pi.setAttribute("Name", "sProdId" + i);
pi.setAttribute("id", "sProdId" + i);
pi.setAttribute("type", "text");
pi.setAttribute("size", "2");
pi.readOnly=true;
td1.appendChild(pi);

var pb = document.createElement("INPUT");
pb.setAttribute("type", "button");
pb.setAttribute("value", "...");
pb.setAttribute("onclick", "pickProd(sProdName"+ i +".value,'a','"+i+"')");
td1.appendChild(pb);
tr.appendChild(td1);

var td2 = document.createElement('td');
var pq = document.createElement("INPUT");
pq.setAttribute("type", "text");
//pq.setAttribute("placeholder", "Product Name");
pq.setAttribute("Name", "iQty" + i);
pq.setAttribute("id", "iQty" + i);
td2.appendChild(pq);
tr.appendChild(td2);

var td3 = document.createElement('td');
var g = document.createElement("IMG");
g.setAttribute("src", "images/delete.png");
g.setAttribute("onclick", "remStkEntry('recTbl','tr" + i + "')");
td3.appendChild(g);

tr.setAttribute("id", "tr" + i);
tr.appendChild(td3);

document.getElementById("recTbl").appendChild(tr);
document.getElementById("iLineCount").setAttribute("value", (i+1)); 
}

/* Function to delete input stock product entry. */
function remStkEntry(parentDiv, childDiv){
if (childDiv == parentDiv){
	alert("The Parent Entry record cannot be removed.");
}
else if (document.getElementById(childDiv)){
	var child = document.getElementById(childDiv);
	var parent = document.getElementById(parentDiv);
	parent.removeChild(child);
}
else{
	alert("Child div has already been removed or does not exist.");
return false;
}
}

/* Function for Text area & set delete link */
function textareaFunction(){
var r = document.createElement('span');
var y = document.createElement("TEXTAREA");
var g = document.createElement("A"); //--KM
y.setAttribute("cols", "17");
y.setAttribute("placeholder", "message..");
//g.setAttribute("src", "delete.png"); //KM
g.setAttribute("href", "#");//KM

var node = document.createTextNode("Delete");
g.appendChild(node);

increment();
y.setAttribute("Name", "textelement_" + i);
r.appendChild(y);
g.setAttribute("onclick", "removeElement('myForm','id_" + i + "')");
r.appendChild(g);
r.setAttribute("id", "id_" + i);
document.getElementById("myForm").appendChild(r);
}

function resetElements(){
document.getElementById('myForm').innerHTML = '';
}