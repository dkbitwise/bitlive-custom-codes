<?php
/**
 * Student registration
 */
?>
<div style="width: 100%!important" class="tml tml-register">
    <form id="new_studnet_reg" name="register_student" method="post">
        <div class="registerdetails">
	        <div class="col-md-3"></div>
            <div class="col-md-6">

                <div style="padding-top: 0px!important" class="studentdetails">
                    <div style="margin-top: 0px!important" class="tml-field-wrap tml-student_heading-wrap">Student Details</div>
                </div>

                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">

                        <div id="stu1" class="panel-collapse collapse in">
                            <div class="panel-body">

                                <div class="tml-field-wrap tml-first_name-wrap"><span class="tml-label">First Name <span>*</span></span>
                                    <input tabindex="9" name="first_name" type="text" id="first_name1" value="" class="tml-field">
                                    <span class="first_name1-error error" style="display:none;">Please Enter First Name</span>
                                </div>

                                <div class="tml-field-wrap tml-last_name-wrap">
                                    <span class="tml-label">Last Name <span>*</span></span>
                                    <input tabindex="10" name="last_name" type="text" value="" id="last_name1" class="tml-field">
                                    <span class="last_name1-error error" style="display:none;">Please Enter Last Name</span>
                                </div>

                                <div class="tml-field-wrap tml-user_email-wrap">
                                    <label class="tml-label" for="user_email1">Email <span>*</span></label>
                                    <input tabindex="11" name="user_email" type="email" value="" id="user_email1" class="tml-field">
                                    <span class="user_email1-error error" style="display:none;">Please Enter Email </span>
                                </div>

                                <div class="tml-field-wrap tml-phone-wrap">
                                    <label class="tml-label" for="phone1">Phone <span>*</span></label>
                                    <div class="intl-tel-input">
                                        <input tabindex="12" name="phone" type="tel" value="" id="phone1" data-id="1" class="tml-field" autocomplete="off" placeholder="(201) 555-0123">
                                        <span class="phone1-error error" style="display:none;">Please Enter Phone number</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="tml-field-wrap tml-timezone-wrap">
                    <label class="tml-label" for="timezone">Timezone <span>*</span></label>
                    <select name="timezone" id="timezone" class="tml-field">
                        <option value="NA" selected="selected">Select Timezone</option>
                        <option value="America/Los_Angeles">(UTC-08:00) Pacific Time (US &amp; Canada)</option>
                        <option value="US/Mountain">(UTC-07:00) Mountain Time (US &amp; Canada)</option>
                        <option value="US/Central">(UTC-06:00) Central Time (US &amp; Canada)</option>
                        <option value="US/Eastern">(UTC-05:00) Eastern Time (US &amp; Canada)</option>
                    </select>
                    <span class="timezone-error error" style="display:none;">Please Select TimeZone</span>
                </div>

				<?php if ( function_exists( 'gglcptch_display' ) ) {
					echo gglcptch_display();
				}; ?>

            </div>
	        <div class="col-md-3"></div>

            <div class="col-md-12">
                <input type="hidden" name="formtype" id="formtype" value="student_register">
                <div class="alreadymember" style="margin: 0px 386px 0px;">Already Registered? Click here to <a href="/login">Login</a></div>
                <div style="margin-top: 0px!important" class="tml-field-wrap tml-submit-wrap">
                    <button type="button" id="reg_stu_submit" name="bitlivcc_reg_stu_submit" value="Register" style="margin-top: 13px;" class="tml-button">Register</button>
                </div>

            </div>

        </div>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function( $ ) {
        var phone1 = $('#phone1');
        phone1.intlTelInput({
            nationalMode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            allowDropdown: true,
            onlyCountries: ["us"],
            preferredCountries: ["us"]
        });

        phone1.keyup(function () {
            if ($('.phone1_msg').length === 0) {
                $('.tml-label[for="phone1"]').append(' <span class="phone1_msg"></span>')
            }
            if ($.trim(phone1.val())) {
                if (phone1.intlTelInput("isValidNumber")) {
                    var getCode = phone1.intlTelInput('getSelectedCountryData').dialCode;
                    $('.phone1_msg').html('✓ Valid');
                    $('.phone1_msg').removeClass('error').addClass('success');
                    $('#phone1_code').val(getCode);
                    $('.phone1-error').css('display', 'none');
                } else {
                    $('.phone1_msg').html('Invalid');
                    $('.phone1_msg').removeClass('success').addClass('error');
                    $('#phone1_code').val('');
                }
            }
        });
    });
</script>
