
//ADD Quantity to url
var btnsAdd = document.getElementsByClassName('js-add-quantity');
var quantities = document.getElementsByClassName('input-number');

var minus = document.getElementsByClassName("btn-minus");
var plus = document.getElementsByClassName("btn-plus");

var staticHref = "http://localhost:8000/cart/add/";
// console.log(btnsAdd);
function changeHrefValue(e)
{
    var btn = e.target.parentNode.parentNode.nextElementSibling.firstChild.nextElementSibling;
    const a = btn.href;
    // console.log(a);
    var b = a.slice(-2);
    btn.href = btn.href.replace(b,'-'+e.target.value);
    // console.log(b);
    //console.log(e.target.value);
    /*btnsAdd.forEach(function(btn){
         const a = btn.href;
         console.log(a);
         var b = a.slice(-2);
        // console.log(a);
        //staticHref += a.slice(staticHref.length);
        btn.href = btn.href.replace(b,'/'+e.target.value);
        console.log(btn.href);
        //btn.href = staticHref +'/'+e.target.value;
        //btn.href = btn.href.concat('-',e.target.value);

    });*/
}
quantities.forEach(function(quantity){

    quantity.addEventListener("change",changeHrefValue)
});

function minusOne(event){
    // event.preventDefault();
    var b = event.target.parentNode;
    b = b.dataset.field;
    var input = document.querySelector("[name='"+b+"']");
    // console.log(input.value);
    //console.log(input);
    // console.log(input.value);
    input.focus();
    input.blur();

}
minus.forEach(function(e){
    e.addEventListener("click",minusOne)    
});