// shipping page 
var shipvistaShippingSellected = 0;
jQuery(function ($) {
    if (document.getElementById('billing_full_names')) {
        document.getElementById('billing_full_names').addEventListener("keyup", event => {
            var fn = document.getElementById('billing_full_names').value.trim().split(' ');
            if (fn.length > 1) {
                document.getElementById('billing_first_name').value = fn[0];
                fn.splice(0, 1);
                var lastname = fn.join(' ');
                document.getElementById('billing_last_name').value = lastname;
            }
        });
    }

    if (document.getElementById('shipping_full_names')) {
        document.getElementById('shipping_full_names').addEventListener("keyup", event => {
            var fn = document.getElementById('shipping_full_names').value.trim().split(' ');
            if (fn.length > 1) {
                document.getElementById('shipping_first_name').value = fn[0];
                fn.splice(0, 1);
                var lastname = fn.join(' ');
                document.getElementById('shipping_last_name').value = lastname;
            }
        });
    }

});

var viewChange = 0;



function shipvistaSelected(id) {
    if (id >= 0) {
        setTimeout(() => {
            shipvistaShippingSellected = id;
            shipvistaActiveOption(id);
            viewChange = 1;
        }, 1000);
    }
}


function shipvistaActiveOption(id) {
    try {
        document.getElementById('_shipvistaListingInView').innerHTML = ``;
        document.getElementById('_shipvistaListingInView').removeAttribute('class');
        if (id > 2) {
            var list = document.getElementById('_shpvistaShippingList').getElementsByTagName('li')[id];
            var content = list.innerHTML.replace('shipvista_list_hide', '').replace('sv_d-none', '');
            document.getElementById('_shipvistaListingInView').innerHTML = '<div class="sv_list-group-item sv_text-left sv_m-0 sv_mb-3">' + content + '</div>';
            document.getElementById('_shipvistaListingInView').setAttribute('class', 'sv_mt-2');
            var list = document.getElementById('_shpvistaShippingList').getElementsByTagName('li')[id].setAttribute('class', 'sv_d-none');

            // shipvistaShippingSellected = 0;
        }
    } catch (e) {}
}

var isMissingAddress = false;

function structurelabelText(text) {
    //console.log(text);
    if (text) {
        text = text.trim();
        var title = {
            'recommended': 'primary  sv_text-white',
            'pickup': 'danger sv_text-white',
            'fastest': 'warning sv_text-white',
            "cheapest": 'success  sv_text-white',
            'address': 'danger  sv_text-white'
        }

        var sp2 = text.split(' ');
        if (Object.keys(title).indexOf(sp2[0].toLowerCase()) >= 0) {
            text = text.replace(' ', ': ');
        }

        var split = text.split(':');
        var badge = '';
        for (let index = 0; index < split.length; index++) {
            const element = split[index];
            if (split.length > 1 && Object.keys(title).indexOf(element.toLowerCase().trim()) >= 0) {
                if (element.toLowerCase().trim() == 'address') {
                    isMissingAddress = true;
                }
                var bsClass = Object.values(title)[Object.keys(title).indexOf(element.toLowerCase().trim())];
                badge += `<small class="sv_badge sv_badge-${bsClass}">${element}</small>`;
                split.splice(index, 1);
                if (element.toLowerCase().trim() == 'pickup') {
                    var con = split.join(':');
                    split = [con];
                    var str = split[0];
                    var ex = str.split('@Address');
                    var txt = ex[0];
                    if (ex.length == 2) {
                        txt += ' <br> <small> <i class="fa fa-map-marker"></i> ' + ex[1] + ' </small> ';
                    }
                    split[0] = txt;
                }
            }
        }



        // check if there is a discount 
        if (split.length > 1) {
            var has = [];
            for (let index2 = 0; index2 < split.length; index2++) {
                const element2 = split[index2];
                var split2 = element2.split('%');


                if (split2.length > 1 && has.length == 0) {
                    badge += `<small class="">${element2}</small>`;
                    has.push(index2);
                } else if (split2.length > 1) {
                    has.push(index2);
                }

            }

            for (let i = 0; i < has.length; i++) {
                const element = has[i];
                split[element] = '';
            }
        }

        if (badge.length > 0) {
            badge += '<br>';
        }


        var label = split.join(' ');
        return badge + label;


    }
}

function toggleCartShippingFields() {
    var list = document.getElementsByClassName('sv_toggle_cart_shipping');
    for (var i = 0; i < list.length; i++) {
        var ele = list[i];
        if (ele.classList.contains('sv_d-none')) {
            ele.classList.remove('sv_d-none');
            if(document.getElementsByClassName('shipping-calculator-form')[0]){
                // document.getElementsByClassName('shipping-calculator-form')[0].viewChangeStyle.display = 'block';
            }
        } else {
            ele.classList.add('sv_d-none');
            if(document.getElementsByClassName('shipping-calculator-form')[0]){
                // document.getElementsByClassName('shipping-calculator-form')[0].viewChangeStyle.display = 'none';
            }
        }
    }
}


function structureCartView() {
	if(!document.getElementsByClassName('sv_list-group').length){
    try {
        var list = document.getElementsByClassName('woocommerce-shipping-totals')[0].getElementsByTagName('li');
    } catch (e) {
        try {
            var list = document.getElementsByClassName('woocommerce-shipping-methods')[0].getElementsByTagName('li');
        } catch (e) {
            var list = [];
        }
    }
	
	if(list.length > 0 ){
        isMissingAddress = false;
    }

    var url = window.location.href;
    var exp = url.split('/');
    try {
        currentPage = exp[3].toLowerCase();
    } catch (e) {}

    if (currentPage != 'checkout' || currentPage != 'cart') {
        if (document.getElementsByClassName('cart_totals ') && document.getElementsByClassName('cart_totals ').length > 0) {
            currentPage = 'cart';
            // isMissingAddress = true;
        } else {
            currentPage = 'checkout';
        }
    }

    var li = '';
    for (var i = 0; i < list.length; i++) {
        try {
            var element = list[i];
            var input = element.getElementsByTagName('input')[0];
            if (!input) {
                continue;
            }
            input.setAttribute("onclick", `shipvistaSelected(${i})`);
            var label = element.getElementsByTagName('label')[0];


            try {
                var price = label.removeChild(label.lastElementChild);
                var priceText = price.outerHTML;
                if (priceText == '<br>') {
                    priceText = 'Free';
                }
            } catch (e) {
                var priceText = 'Free';
            }
            var labelText = structurelabelText(label.innerText);
            label.innerHTML = labelText;
            var labelView = label; //.outerHTML();

            var hideListClass = '';
            if (i > 2 && li.length > 0) {
                hideListClass = 'shipvista_list_hide sv_d-none';
            }
            // add class of inpu
            input.setAttribute('class', 'sv_radio')
            if (input.checked == true) {
                shipvistaShippingSellected = i;
            }

            // structure cart page
            if (currentPage == 'cart' && isMissingAddress == true) {
                // add display none to all classes
                document.getElementsByClassName('woocommerce-shipping-methods')[0].classList.add('sv_d-none', 'sv_toggle_cart_shipping');
                document.getElementsByClassName('woocommerce-shipping-destination')[0].classList.add('sv_d-none', 'sv_toggle_cart_shipping');
                document.getElementsByClassName('woocommerce-shipping-destination')[0].innerHTML = '';

                document.getElementsByClassName('woocommerce-shipping-calculator')[0].classList.add('sv_d-none', 'sv_toggle_cart_shipping');
                document.getElementById('calc_shipping_state_field').classList.add('sv_d-none');
                document.getElementById('calc_shipping_city_field').classList.add('sv_d-none');

                var parent = document.getElementsByClassName('woocommerce-shipping-methods')[0].parentNode;
                var appendHeader = '';
                if (parent.getElementsByTagName('h4')[0] && !document.getElementById('sv_calculateBtnToggle')) {
                    parent.getElementsByTagName('h4')[0].remove();
                    appendHeader = `
                    <div class="sv_flex-fill"><h4>Shipping</h4></div>`;
                }
                var html = parent.innerHTML;
                if(!document.getElementById('sv_calculateBtnToggle')){
                parent.innerHTML = `
                   <div class="sv_d-flex">
                        <div class="sv_flex-fill">
                        <h4>Shipping</h4></div>
                        <div class="pl-2"><a id="sv_calculateBtnToggle" onclick="toggleCartShippingFields()" class="sv_btn sv_btn-sm sv_btn-danger sv_text-white"><small>Calculate Now</small></a></div>
					</div>
				` + html;
                }

            }

            var appendShippingTitle = '';
            var colSpan = '';
            var stamp = '';
            if (isMissingAddress == true) {
                // ${input.outerHTML}
                // ${labelView.outerHTML}
                var li = '';
            } else {
                if (currentPage == 'cart' && i == 0) {
                    colSpan = 'colspan="2"';
                    document.getElementById('calc_shipping_state_field').classList.add('sv_d-none');
                    document.getElementById('calc_shipping_city_field').classList.add('sv_d-none');
                    document.getElementsByClassName('woocommerce-shipping-calculator')[0].classList.add('sv_d-none', 'sv_toggle_cart_shipping');

                    try {
                        var ps = document.getElementById('calc_shipping_postcode').value + ', ' + document.getElementById('calc_shipping_country').options[document.getElementById('calc_shipping_country').selectedIndex].innerText;
                        document.getElementsByClassName('woocommerce-shipping-destination')[0].innerHTML = 'Shipping to <strong>' + ps + '<strong>';
                    } catch (e) {
                        console.log(e);
                    }
                    document.getElementsByClassName('woocommerce-shipping-destination')[0].innerHTML = `<a class="sv_float-right sv_btn sv_btn-text sv_text-danger sv_m-0" style="margin-top:-8px !important"  onclick="document.getElementsByClassName('woocommerce-shipping-calculator')[0].classList.remove('sv_d-none')"> <small>Update</small></a>` + document.getElementsByClassName('woocommerce-shipping-destination')[0].innerHTML;

                }

                stamp = `<div class="sv_text-right sv_w-100 "><small style="color:#4B4B4B" ><i>Live rates powered by <b>shipvista.com</b></i></small></div>`;

                //if (currentPage == 'checkout') {
                    appendShippingTitle = '<h4 class="sv_shipping-title">Shipping</h4>';
                //}

                li += `
                    <li class="sv_list-group-item sv_text-left sv_m-0 ${hideListClass}">
                        <div class="sv_d-flex">
                            <div class="sv_px-2 sv_align-self-center">
                                ${input.outerHTML}
                            </div>
                            <div class="sv_flex-fill sv_border-right sv_align-self-center">
                                ${labelView.outerHTML}
                            </div>
                            <div class="sv_align-self-center sv_pl-2">
                                <b>
                                    ${priceText}
                                </b>
                            </div>
                        </div>
                    </li>
            `;
            }
        } catch (e) {
            console.log('Shiplist order not supported');
        }

    }

    if (li.length > 0) {
        if (list.length > 3) {
            li += `
                <li class="sv_list-group-item sv_m-0 sv_text-center sv_text-dark sv_bg-light" onclick="shipvistaToggleViewMoreList()">
                    <a href="javascript:void(0)"  class="sv_text-dark "><small id="_shipvistaMoreList">MORE <i class="fa fa-chevron-down"></i></small></a>
                </li>
            `;
        }
        if (currentPage == 'cart') {
            // shortern address for shipping to
            // get the post code


            var card = `
                <tr>
                    <th colspan="2">
                        ${appendShippingTitle}
                    <div id="_shipvistaListingInView"></div>
                                       
                    <div class="my-2">
                        <ul class="sv_list-group " id="_shpvistaShippingList">
                        ${li}
                        </ul>
                    </div>
                    ${stamp}
                    </th>
                </tr>
            `;
        } else {

            var card = `
                <tr>
                    <th colspan="2">
                        ${appendShippingTitle}
                    <div id="_shipvistaListingInView"></div>
                                   
                    <div class="my-2">
                        <ul class="sv_list-group" id="_shpvistaShippingList">
                        ${li}
                        </ul>
                    </div>
                    ${stamp}
                    </th>
                </tr>
        `;
        }
		
		try{
			document.getElementsByClassName('woocommerce-shipping-methods')[0].style.display = "block";
		} catch(e) {}
        try {
            document.getElementsByClassName('woocommerce-shipping-totals')[0].innerHTML = card;
        } catch (e) {
            document.getElementsByClassName('woocommerce-shipping-methods')[0].innerHTML = card;
        }
    }    
}

}

function sv_supmitPostalCode() {
    var code = document.getElementById('billing_postcode').value;
    var country = document.getElementById('billing_country').value;
    if (code.length > 3 && country.length == 2) {

        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: my_ajax_object.ajax_url,
            data: {
                action: 'shipvista_postcode',
                shipvista_get_postal: code,
                shipvista_get_country: country
            },
            success: function (msg) {
                console.log("\n\n\n", msg);
                if (msg.status == true) {
                    document.getElementById('_shpvistaShippingList').innerHTML = msg.html;
                } else {
                    inputErr('_shpvistaShippingFrmErr', msg.message);
                }
            }
        });

    } else {
        inputErr('_shpvistaShippingFrmErr', 'Please select a valid postal code.');
    }
}

function inputErr(id, msg) {
    document.getElementById(id).innerHTML = `<small class="sv_text-danger">${msg}<small>`;
}

var isMore = false;

function shipvistaToggleViewMoreList() {
    var ell = document.getElementsByClassName('shipvista_list_hide');
    for (let index = 0; index < ell.length; index++) {
        const element = ell[index];
        element.classList.toggle("sv_d-none");

    }
    if (isMore == false) {
        document.getElementById('_shipvistaMoreList').innerHTML = 'LESS <i class="fa fa-chevron-up"></i>';
        isMore = true
    } else {
        document.getElementById('_shipvistaMoreList').innerHTML = 'MORE <i class="fa fa-chevron-down"></i>';
        isMore = false;
    }
}


var currentPage = '';

function initDisplay() {
    //re-do your jquery
    setTimeout(() => {
        try {
			
            isMissingAddress = false;
            structureCartView();
            document.getElementById('_shpvistaShippingList').getElementsByTagName('input')[shipvistaShippingSellected].checked = true;
            shipvistaActiveOption(shipvistaShippingSellected);
        } catch (e) {
            console.log('Failed to load ell', e);
        }
    }, 500);
    // 
}

jQuery(document).on('updated_checkout', function () {
    initDisplay();
});

jQuery(document.body).on('updated_cart_totals', function () {
    initDisplay();
});

function removeBtnEventListener(){
	if(this.hasAttribute('href')){
        this.remove();
	}
}

var isEventSet = false;
function checkStructureChanges(){
var change = setInterval(()=>{
		
    var url = window.location.href;
    var exp = url.split('/');
    try {
        currentPage = exp[3].toLowerCase();
    } catch (e) {}

    if (currentPage != 'checkout' || currentPage != 'cart') {
        if (document.getElementsByClassName('cart_totals ') && document.getElementsByClassName('cart_totals ').length ) {
            currentPage = 'cart';
            // isMissingAddress = true;
        } else {
            currentPage = 'checkout';
        }
    }
	
	if(currentPage == 'cart' && !isEventSet ){
	try{
				var removeBtns = document.getElementsByClassName('remove');
				for(var i = 0; i < removeBtns.length; i++){
					const element = removeBtns[i];
					element.addEventListener("click", removeBtnEventListener);
				}
		isEventSet = true;
			} catch(e){}
	}
	
	if(!document.getElementById("sv_calculateBtnToggle") ){
		if(!document.getElementsByClassName('sv_list-group').length){
			if(!document.getElementById("sv_loaderId") && document.getElementsByClassName('woocommerce-shipping-methods')[0]){
				isEventSet = false;
				var loader = document.createElement('div');
				loader.innerHTML = loaderContainer;
				loader.setAttribute('id', "sv_loaderId");
				document.getElementsByClassName('woocommerce-shipping-methods')[0].append(loader);
		    }
			setTimeout(() => {
			if(document.getElementById("sv_loaderId")){
				document.getElementById("sv_loaderId").remove();
			}
			structureCartView();
			}, 1500);
		} else {
			
	}
	}
}, 500);
}
structureCartView();
checkStructureChanges();

// GET USER LOCATION