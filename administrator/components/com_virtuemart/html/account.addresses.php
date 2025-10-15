<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

/* Set Dynamic Page Title when applicable */
$mainframe->setPageTitle($VM_LANG->_PHPSHOP_ACCOUNT_TITLE);
if ($perm->is_registered_customer($auth['user_id'])) {
    global $mosConfig_live_site, $iso_client_lang;
    $userId = (int)$_SESSION['auth']['user_id'];
    $db = new ps_DB;

    $queryAddresses = "
        SELECT
            ui.first_name,
            ui.last_name, 
            ui.city, 
            ui.zip,
            s.state_name AS state,
            ui.suite, 
            ui.street_number, 
            ui.street_name,
            ui.phone_1 AS phone,
            ui.user_info_id,
            ui.user_email
        FROM jos_vm_user_info AS ui
        LEFT JOIN jos_vm_state AS s ON s.state_2_code = ui.state
        WHERE ui.user_id = " . (int)$userId . "
            AND ui.address_type = 'ST'
        ORDER BY ui.cdate DESC";

    $db->setQuery($queryAddresses);
    $addresses = $db->loadObjectList();

    $queryStates = "SELECT state_2_code, state_name FROM jos_vm_state WHERE country_id = 13 ORDER BY state_name";
    $db->setQuery($queryStates);
    $states = $db->loadObjectList();

    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <div class="container white">
        <div class="row">
            <div class="col-xs-12 col-sm-12 delivery_inner">
                <h3><?php echo $VM_LANG->_CHECKOUT_MY_ADDRESSES; ?></h3>
                <div id="shipping_addresses" class="address-grid">
                    <?php foreach ($addresses as $index => $address): ?>
                        <div class="address-item"
                             data-user-info-id="<?php echo $address->user_info_id; ?>"
                             data-email="<?php echo $address->user_email; ?>"
                             data-first-name="<?php echo htmlspecialchars($address->first_name); ?>"
                             data-last-name="<?php echo htmlspecialchars($address->last_name); ?>"
                             data-suite="<?php echo htmlspecialchars($address->suite); ?>"
                             data-street-name="<?php echo htmlspecialchars($address->street_name); ?>"
                             data-street-number="<?php echo htmlspecialchars($address->street_number); ?>"
                             data-city="<?php echo htmlspecialchars($address->city); ?>"
                             data-state="<?php echo htmlspecialchars($address->state); ?>"
                             data-zip="<?php echo htmlspecialchars($address->zip); ?>"
                             data-phone="<?php echo htmlspecialchars($address->phone); ?>">

                            <button class="delete-address-btn" data-user-info-id="<?php echo $address->user_info_id; ?>">✖</button>

                            <div class="address-content">
                                <strong><?php echo htmlspecialchars($address->first_name);  ?></strong>
                                <strong><?php echo htmlspecialchars($address->last_name);  ?></strong><br>
                                <?php echo htmlspecialchars($address->suite); ?>
                                <?php echo htmlspecialchars($address->street_number); ?>
                                <?php echo htmlspecialchars($address->street_name); ?><br>
                                <?php echo htmlspecialchars(implode(', ', array_filter([$address->city, $address->state, $address->zip]))); ?><br>
                                <?php echo htmlspecialchars($address->phone); ?>
                                <?php echo htmlspecialchars($address->user_email); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        let states = <?php echo json_encode($states); ?>;
        let VM_LANG = <?php echo json_encode($VM_LANG); ?>;
        document.addEventListener("DOMContentLoaded", function () {
            let addressItems = document.querySelectorAll(".address-item");

            addressItems.forEach(function (item) {
                item.addEventListener("click", function () {
                    if (!event.isTrusted) {
                        return;
                    }
                    let id = item.getAttribute("data-user-info-id");
                    let firstName = item.getAttribute("data-first-name");
                    let lastName = item.getAttribute("data-last-name");
                    let suite = item.getAttribute("data-suite") || '';
                    let streetNumber = item.getAttribute("data-street-number");
                    let streetName = item.getAttribute("data-street-name");
                    let city = item.getAttribute("data-city");
                    let state = item.getAttribute("data-state");
                    let zip = item.getAttribute("data-zip");
                    let phone = item.getAttribute("data-phone");
                    let email = item.getAttribute("data-email");
                    let stateOptions = states.map(stateItem => {
                        let isSelected = (stateItem.state_2_code === state || stateItem.state_name === state) ? "selected" : "";
                        return `<option value="${stateItem.state_2_code}" ${isSelected}>${stateItem.state_name}</option>`;
                    }).join("");
                    Swal.fire({
                        title: VM_LANG._TTL_UPDATE_ADDRESS,
                        html: `
                    <div class="swal2-form-group">
                        <label for="editFirstName"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_FIRST_NAME; ?></label>
                        <input id="editFirstName" class="swal2-input" value="${firstName}">
                    </div>
                    <div class="swal2-form-group">
                        <label for="editLastName"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_LAST_NAME; ?></label>
                        <input id="editLastName" class="swal2-input" value="${lastName}">
                    </div>
                    <div class="swal2-form-group">
                        <label for="editEmail"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_EMAIL; ?></label>
                        <input id="editEmail" class="swal2-input" type="email" value="${email}">
                    </div>
                     <div class="swal2-form-group">
                        <label for="editSuite"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_SUITE; ?></label>
                        <input id="editSuite" class="swal2-input" value="${suite}">
                    </div>
                    <div class="swal2-form-group">
                        <label for="editStreetNumber"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_STREET_NUMBER; ?></label>
                        <input id="editStreetNumber" class="swal2-input" value="${streetNumber}">
                    </div>
                    <div class="swal2-form-group">
                        <label for="editStreetName"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_STREET_NAME; ?></label>
                        <input id="editStreetName" class="swal2-input" value="${streetName}">
                    </div>
                    <div class="swal2-form-group">
                        <label for="editCity"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_CITY; ?></label>
                        <input id="editCity" class="swal2-input" value="${city}">
                    </div>
                    <div class="swal2-form-group">
                        <label><?php echo $VM_LANG->_PHPSHOP_USER_FORM_STATE; ?></label>
                        <select id="editState" class="swal2-select">${stateOptions}</select>
                    </div>
                    <div class="swal2-form-group">
                        <label for="editZip"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_ZIP; ?></label>
                        <input id="editZip" class="swal2-input" value="${zip}">
                    </div>
                    <div class="swal2-form-group">
                        <label for="editPhone"><?php echo $VM_LANG->_PHPSHOP_USER_FORM_PHONE; ?></label>
                        <input id="editPhone" class="swal2-input" value="${phone}">
                    </div>
                `,
                        showCancelButton: true,
                        confirmButtonText: VM_LANG._BTN_SAVE,
                        cancelButtonText: VM_LANG._BTN_BACK,
                        didOpen: () => {
                            const phoneInput = document.getElementById("editPhone");
                            phoneInput.addEventListener("input", function () {
                                let numbersOnly = phoneInput.value.replace(/\D/g, "");
                                if (numbersOnly.length > 10) {
                                    numbersOnly = numbersOnly.substring(0, 10);
                                }
                                if (numbersOnly.length === 10) {
                                    phoneInput.value = numbersOnly.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3");
                                }
                            });

                            const emailInput = document.getElementById("editEmail");
                            emailInput.addEventListener("input", function () {
                                emailInput.value = emailInput.value.trim();
                            });

                        },
                        preConfirm: () => {
                            let isValid = true;
                            function showError(inputId, message) {
                                let input = document.getElementById(inputId);
                                if (!input) return;
                                input.classList.add("error-input");
                                let errorLabel = document.createElement("div");
                                errorLabel.className = "error-message";
                                errorLabel.innerText = message;
                                if (!input.nextElementSibling || !input.nextElementSibling.classList.contains("error-message")) {
                                    input.parentNode.appendChild(errorLabel);
                                }
                                isValid = false;
                            }

                            function clearError(inputId) {
                                let input = document.getElementById(inputId);
                                if (!input) return;
                                input.classList.remove("error-input");
                                if (input.nextElementSibling && input.nextElementSibling.classList.contains("error-message")) {
                                    input.nextElementSibling.remove();
                                }
                            }

                            let fields = [
                                "editFirstName",
                                "editLastName",
                                "editStreetNumber",
                                "editStreetName",
                                "editCity",
                                "editZip",
                                "editPhone",
                                "editEmail"
                            ];

                            fields.forEach(field => {
                                let value = document.getElementById(field).value.trim();
                                if (!value) {
                                    showError(field, VM_LANG._FILD_REQUIRE);
                                } else {
                                    clearError(field);
                                }
                            });


                            let phoneInput = document.getElementById("editPhone").value.trim();
                            let phoneRegex = /^\d{3}-\d{3}-\d{4}$/;
                            if (!phoneRegex.test(phoneInput)) {
                                showError("editPhone", VM_LANG._FILD_PHONE_INVALID);
                            } else {
                                clearError("editPhone");
                            }

                            let emailInput = document.getElementById("editEmail").value.trim();
                            let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                            if (!emailRegex.test(emailInput)) {
                                showError("editEmail", VM_LANG._FILD_EMAIL_INVALID);
                            } else {
                                clearError("editEmail");
                            }

                            if (!isValid) {
                                return false;
                            }

                            return {
                                id: id,
                                firstName: document.getElementById("editFirstName").value,
                                lastName: document.getElementById("editLastName").value,
                                streetNumber: document.getElementById("editStreetNumber").value,
                                streetName: document.getElementById("editStreetName").value,
                                city: document.getElementById("editCity").value,
                                zip: document.getElementById("editZip").value,
                                phone: document.getElementById("editPhone").value,
                                email: document.getElementById("editEmail").value,
                                suite: document.getElementById("editSuite").value.trim(),
                                stateShort: document.getElementById("editState").value,
                                state: document.getElementById("editState").selectedOptions[0].text
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let editedData = result.value;
                            item.setAttribute("data-first-name", editedData.firstName);
                            item.setAttribute("data-last-name", editedData.lastName);
                            item.setAttribute("data-suite", editedData.suite);
                            item.setAttribute("data-street-number", editedData.streetNumber);
                            item.setAttribute("data-street-name", editedData.streetName);
                            item.setAttribute("data-city", editedData.city);
                            item.setAttribute("data-state", editedData.state);
                            item.setAttribute("data-zip", editedData.zip);
                            item.setAttribute("data-email", editedData.email);
                            item.setAttribute("data-phone", editedData.phone);
                            item.querySelector(".address-content").innerHTML = `
                        <strong>${editedData.firstName} ${editedData.lastName}</strong><br>
                        ${editedData.suite} ${editedData.streetNumber} ${editedData.streetName}<br>
                        ${editedData.city}, ${editedData.state}, ${editedData.zip}<br>
                        ${editedData.phone} ${editedData.email}
                    `;

                            jQuery.ajax({
                                url: '/index.php?option=com_ajaxorder&task=updateUserAddress',
                                type: 'POST',
                                data: JSON.stringify(editedData),
                                contentType: 'application/json',
                                success: function(response) {
                                    let parsedResponse;
                                    try {
                                        parsedResponse = typeof response === "string" ? JSON.parse(response) : response;
                                    } catch (error) {
                                        Swal.fire(VM_LANG._ERROR, VM_LANG._SOMENTHING, "error");
                                        return;
                                    }
                                    if (parsedResponse.result === true) {
                                        Swal.fire({
                                            icon: "success",
                                            title: VM_LANG._TITLE_ADDRESS_UPDATED,
                                            confirmButtonText: VM_LANG._BTN_OK
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: VM_LANG._MSG_ADDRESS_FAILED,
                                            confirmButtonText: VM_LANG._BTN_OK
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error(" **AJAX Request Error:**", error);
                                    console.error(" **XHR Response Text:**", xhr.responseText);
                                    console.error(" **HTTP Status Code:**", xhr.status);
                                    Swal.fire(VM_LANG._ERROR, "Server response: " + error, "error");
                                }
                            });
                        }
                    });
                });
            });
        });
        let deleteButtons = document.querySelectorAll(".delete-address-btn");

        deleteButtons.forEach(function (btn) {
            btn.addEventListener("click", function (event) {
                event.stopPropagation();

                let userInfoId = btn.getAttribute("data-user-info-id");

                Swal.fire({
                    title: VM_LANG._TTL_DELETE_CONFIRM,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: VM_LANG._BTN_YES,
                    cancelButtonText: VM_LANG._BTN_NO
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/index.php?option=com_ajaxorder&task=deleteUserAddress',
                            type: 'POST',
                            data: JSON.stringify({ id: userInfoId }),
                            contentType: 'application/json',
                            success: function (response) {
                                let parsedResponse = typeof response === "string" ? JSON.parse(response) : response;
                                if (parsedResponse.result === true) {
                                    Swal.fire({
                                        icon: "success",
                                        title: VM_LANG._DELETE_SUCCESS_TEXT,
                                        confirmButtonText: VM_LANG._BTN_OK
                                    }).then(() => {
                                        btn.closest(".address-item").remove();
                                    });
                                } else if (parsedResponse.error) {
                                    Swal.fire({
                                        icon: "error",
                                        title: parsedResponse.error,
                                        confirmButtonText: VM_LANG._BTN_OK
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: VM_LANG._ERROR,
                                        text: "Unknown error occurred.",
                                        confirmButtonText: VM_LANG._BTN_OK
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                Swal.fire(VM_LANG._ERROR, VM_LANG._DELETE_SERVER_ERROR + error, "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
    <style>
        .address-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .address-item {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: calc(33.33% - 7px);
            border: 2px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            background: #fff;
        }

        .address-item:hover {
            background: #dfdfdf;
        }

        .address-content {
            padding-left: 10px;
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            .address-item {
                width: 100%;
            }
        }

        .swal2-popup {
            width: 500px !important; /* Уменьшаем ширину */
            max-width: 90%;
            padding: 20px !important;
        }

        .swal2-html-container {
            text-align: left !important;
            width: 100%;
        }

        .swal2-form-group {
            margin-bottom: 8px;
            text-align: left;
            width: 100%;
        }

        .swal2-form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .swal2-input,
        .swal2-select {
            width: 100% !important;
            padding: 10px !important;
            box-sizing: border-box;
            font-size: 14px;
            margin: 0 !important;
            border: 2px solid #d9d9d9;
            border-radius: 5px;
            background: white;
            outline: none;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .swal2-input:focus,
        .swal2-select:focus {
            border-color: #B1CAE3;
            box-shadow: 0 0 5px rgba(177, 202, 227, 0.8);
        }

        .swal2-confirm, .swal2-cancel {
            font-size: 14px !important;
            padding: 8px 20px !important;
        }

        .error-input {
            border: 2px solid red !important;
            background-color: #ffe6e6 !important;
        }
        .delete-address-btn {
            position: absolute;
            top: 3px;
            right: 5px;
            border: none;
            background: none;
            font-size: 20px;
            color: red;
            cursor: pointer;
        }
        .delete-address-btn:hover {
            color: darkred;
        }
    </style>

    <?php
} else {
    include(PAGEPATH . 'checkout.login_form.php');
}
?>
