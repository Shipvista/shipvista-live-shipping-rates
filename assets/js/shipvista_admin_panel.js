/**
 * Declare global variables
 */
var idAppend = 'woocommerce_shipvista_';
var sv_apiEndPoint = '';
var sv_apiToken = '';
var sv_alertBar = `<div id="shipvista_alertBar" class="shipvista_alertBar"><div id="shipvista_alertBar_content"></div></div>`;
var sv_action_link = '';
var sv_carrierSettings = {};
var sv_apiUrl = 'https://api.shipvista.com/';
/**
 * Return element content based on id
 * @param {*} id 
 */
function _id(id) {
    return document.getElementById(id);
}

/**
 * Get form parts from wordpress input fields
 */
function getInput(id) {
    return $('#' + idAppend + id).val();
}

/**
 * Manage form error
 * @param id
 * @param msg
 */
function inputError(id, msg) {
    try {
        if (id) {
            var input = idAppend + id;
            scrollToInput(input);
            $('#' + input).addClass('is-invalid');
            $('#' + input).removeClass('mb-3');

            // check for error container
            var parent = _id(input).parentElement;
            var errorId = input + '_msg';
            var span = parent.querySelector('#' + errorId) != undefined;
            if (span != undefined) {
                span = document.createElement('div');
                span.setAttribute('id', errorId);
                span.setAttribute('class', 'text-danger mb-3');
                parent.appendChild(span);
            }
            $('#' + errorId).html(msg)
            // remove error from page after view
            setTimeout(() => {
                $('#' + input).removeClass('is-invalid');
                $('#' + input).addClass('mb-3');
                $('#' + errorId).html('')
            }, 5000);
        } else {
            alertBar('alert');
        }
    } catch (e) {
        console.log(e);
    }
}


function alertBar(msg, cls) {
    if (!msg) return false;

    if (!cls) {
        cls = 'bg-danger';
    }

    var errorBar = _id('shipvista_alertBar');
    if (errorBar == undefined) {
        $('body').append(sv_alertBar);
    }
    // add snackbar class
    $('#shipvista_alertBar').addClass(cls);
    // dispaly alert bar

    $('#shipvista_alertBar').addClass('show');
    $('#shipvista_alertBar_content').html(msg);
    setTimeout(() => {
        $('#shipvista_alertBar').attr('class', " ");
    }, 5000);

}

/**
 * Scroll to error form
 */
function scrollToInput(id) {
    _id(id).parentNode.scrollIntoView();
    _id(id).scrollIntoView(true);

    // add space to inview
    var scrolledY = window.scrollY;
    if (scrolledY) {
        window.scroll(0, scrolledY - 100);
    }

}

/**
 * Call shipvista api
 */
function svApiCall(content, endPoint, meth, callback) {
    if (!meth) meth = 'post';
    endPoint = sv_apiUrl + endPoint;
    var result = {
        status: 0,
        message: ''
    };
    // if (Object.keys(content).length > 0) {
    $.ajax({
        url: endPoint,
        type: meth,
        data: JSON.stringify(content),
        contentType: 'application/json',
        dataType: 'json',
        headers: {
            Authorization: 'Bearer ' + sv_apiToken,
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status == 200 || xhr.status == 'success') {
                console.log(data)
                if (Object.keys(data).indexOf('status') >= 0) {
                    result = data;
                } else if (Object.keys(data).indexOf('response') >= 0) {
                    result = data.response;

                } else {
                    data.status = true;
                    result = data;
                }
            } else {
                result.xhr = xhr.status;
                result.message = data.responseText;
            }
            if (callback.length > 0) {
                // find object
                var fn = window[callback];
                // is object a function?
                if (typeof fn === "function") fn(result);
            } else {
                return result;
            }
        },
        error: function (data) {
            result.message = data.responseText;
            if (callback.length > 0) {
                // find object
                var fn = window[callback];
                // is object a function?
                if (typeof fn === "function") fn(result);
            } else {
                return result;
            }
            return false;
        }
    });
    // } else {
    //     result.message = 'Invaild content';
    //     return result;
    // }
}

/**
 * Connect shop to shipvista
 * 
 */
function shipvista_ConnectStore(callback = false) {
    if (callback != false) {
        result = callback;
        if (result.status == true) {
            alertBar('Login successfull', 'bg-success');
            // set form data
            $('#' + idAppend + 'shipvista_api_token').val(result.access_token.tokenString);
            $('#' + idAppend + 'shipvista_refresh_token').val(result.refresh_token.tokenString);
            $('#' + idAppend + 'shipvista_token_expires').val(result.access_token.expireAt);
            $('#' + idAppend + 'shipvista_user_avatar').val(result.avatar);
            $('#' + idAppend + 'shipvista_user_balance').val(0);
            $('#' + idAppend + 'shipvista_user_name').val(result.refresh_token.username);
            $('#' + idAppend + 'shipvista_user_currency').val('USD');
            // set form data
            sv_WooSave();
        } else {
            alertBar(result.message, 'bg-danger');
        }
    } else {
        var email = getInput('shipvista_user_name');
        var password = getInput('shipvista_user_pass');
        if (email.length > 1 && email.length > 3) {
            if (password.length > 4) {
                var content = {
                    user_id: email,
                    password: password
                };

                svApiCall(content, '/api/Login', 'POST', 'shipvista_ConnectStore');

            } else {
                inputError('shipvista_user_pass', 'Enter a valid shipvista account password');
            }
        } else {
            inputError('shipvista_user_email', 'Enter a valid email.');
        }
    }
}

function sv_WooSave() {
    document.getElementsByClassName('woocommerce-save-button')[0].click();
}



// Unlink user account
function shipvista_unlinkAccount() {
    var status = confirm("Are you sure you want to unlink shipvista?");
    if (status == false) {
        _id('woocommerce_shipvista_enabled').checked = true;
        return false;
    }
    _id('woocommerce_shipvista_enabled').checked = false;
    alertBar('Thank you for using shivista.', 'bg-info');
    setTimeout(() => {
        // save 
        sv_WooSave();
    }, 3000);

}

function shipvista_carrierSelectOption(carrier, key) {
    if (!carrier || !key) return false;
    var input = _id(key);
    var carrierStatus = _id(carrier).checked;
    if (carrierStatus == true) {
        var status = _id(key).checked ? 1 : 0;

        input.disabled = false;
        // get name
        var name = input.getAttribute('data-shipvista-name');
        var key = input.getAttribute('name');
        // set values
        sv_carrierSettings[carrier][key].name = name;
        sv_carrierSettings[carrier][key]['checked'] = status;

        jsonText = JSON.stringify(sv_carrierSettings[carrier]);
        _id(idAppend + carrier).value = jsonText;
        console.log(jsonText)
    } else {
        _id(key).checked = false;
        alertBar('Please enable carrier to select this method', 'bg-warning');
    }

}

function shipvista_toggleCarrieSubs(carrier, act) {
    if (!carrier) return false;

    var options = document.getElementsByClassName(carrier + '_options');
    console.log(carrier + '_options')
    if (options.length > 0) {
        for (let index = 0; index < options.length; index++) {
            const parent = options[index];
            var input = parent.getElementsByTagName('input')[0];
            input.checked = act;
            if (act == true) {
                input.disabled = false;
                // get name
                var name = input.getAttribute('data-shipvista-name');
                var key = input.getAttribute('name');
                // set values
                sv_carrierSettings[carrier][key].name = name;
                sv_carrierSettings[carrier][key]['checked'] = 1;
            } else {
                input.disabled = true;

            }
        }
    }

    var jsonText = '';
    if (act == true) {
        jsonText = JSON.stringify(sv_carrierSettings[carrier]);
    }
    _id(idAppend + carrier).value = jsonText;

}

function shipvista_toggleCarrierOption(carrier) {
    if (!carrier) return false;

    var status = _id(carrier).checked;
    if (status == false) {
        shipvista_toggleCarrieSubs(carrier, false);
        _id(idAppend + carrier + '_enabled').value = 'no';
    } else {
        // check if the item exist
        if (Object.keys(sv_carrierSettings).indexOf(carrier) == undefined) {
            sv_carrierSettings[carrier] = {};
        }
        _id(idAppend + carrier + '_enabled').value = 'yes';

        // check if the item exist
        shipvista_toggleCarrieSubs(carrier, true);
    }
}


function shipvista_saveSettings(ell) {

    if (ell == 'shipper') {
        _id(idAppend + 'shipvista_origin_country').value = _id('shipvista_origin_country').value;
        _id(idAppend + 'shipvista_origin_address').value = _id('shipvista_origin_address').value;
        _id(idAppend + 'shipvista_origin_city').value = _id('shipvista_origin_city').value;
        _id(idAppend + 'shipvista_origin_postcode').value = _id('shipvista_origin_postcode').value;
        _id(idAppend + 'shipvista_origin_phone_number').value = _id('shipvista_origin_phone_number').value;
        sv_WooSave();
    }

}


function svToggleClass(id, toggle) {
    if (id) {
        if (!toggle) {
            toggle = 'd-none'
        }
        $('#' + id).toggleClass(toggle);
    }
}



var isMore = false;

function shipvistaToggleViewMoreList() {
    // $('.shipvista_list_hide').toggleClass('sv_d-none');
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

function shipvistaSubmitlabelCreate() {
    document.getElementById('shipvistaLabel_get_label').value = 1;
    document.getElementById('shipvista_shipping_carrier').value = document.querySelector('input[name=shipvista_shipping_method]:checked').getAttribute('data-carrier');
    document.getElementById('shipvista_shipping_options').value = document.querySelector('input[name=shipvista_shipping_method]:checked').getAttribute('data-carrier-option');
    document.getElementById('post').submit();
}


function toggleAccountCreate() {
    $('#_setupAccount').toggleClass('d-none');
    $('#_createAccount').toggleClass('d-none');
}

function createShipvistaUserAccount(callback = false) {
    if (callback != false) {

        if (callback.status == true) {
            alertBar(callback.message, 'bg-success');
            setCookie('shipvista_user_pending_auth', '', 1);
            $('#_signupCont').toggleClass('d-none');
            $('#_verifyCont').toggleClass('d-none');
            $('#_verifyEmail').html(getCookie('shipvista_user_email'));
            $('#_verifyName').html(getCookie('shipvista_user_names'));
        } else {
            alertBar(callback.message, 'bg-danger');
            setCookie('shipvista_user_email', '', -1);
            setCookie('shipvista_user_names', '', -1);
        }
    } else {
        var name = getInput('create_user_names');
        var email = getInput('create_user_email');
        var phone = getInput('create_user_phone');
        var pass = getInput('create_user_password');
        // run creation of details
        if (name.length > 4) {
            if (email.length > 3 && email.split('@').length == 2) {
                if (phone.length > 7) {
                    if (phone.length > 7) {
                        // store cookie
                        setCookie('shipvista_user_email', email, 1);
                        setCookie('shipvista_user_names', name, 1);
                        svApiCall({
                            'contact_name': name,
                            'email': email,
                            'phone': phone,
                            'password': pass,
                            'verficationEmail': getInput('create_user_link')
                        }, '/api/register', 'POST', 'createShipvistaUserAccount');

                    } else {
                        inputError('create_user_password', 'Your password must be atleast 8 characters.');
                    }
                } else {
                    inputError('create_user_phone', 'Please enter a valid phone number.')
                }
            } else {
                inputError('create_user_email', 'Please enter a valid email address');
            }
        } else {
            inputError('create_user_names', 'Please enter a valid name.');
        }
    }
}




function changeSignupDetails() {
    setCookie('shipvista_user_email', '', -1);
    setCookie('shipvista_user_names', '', -1);
    setCookie('shipvista_user_pending_auth', '', -1);
    alertBar('Authentication reseted.', 'bg-info');
    window.location = '';
}
































// system functions
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function checkCookie() {
    var user = getCookie("username");
    if (user != "") {
        alert("Welcome again " + user);
    } else {
        user = prompt("Please enter your name:", "");
        if (user != "" && user != null) {
            setCookie("username", user, 365);
        }
    }
}