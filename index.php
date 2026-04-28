<?php
// Disable all caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
session_start(); // <-- Add this to enable PHP session for CAPTCHA
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>||-Brown Bioprinting & Imaging -||</title>	
    <script src="js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/custom.css?dt=<?php echo time();?>" />
    <meta name="msapplication-TileColor" content="#efefef">
	<meta name="theme-color" content="#efefef">	
	<link href="css/particle.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="css/bootstrap-4.4.1.BBI001.css" rel="stylesheet">
    <style>
      header, section, footer {
        position: relative;
        z-index: 2;
      }
      
      .intercom {
        position: fixed;
        bottom: 32px;
        right: 32px;
        z-index: 9999;
        cursor: pointer;
      }
      
      .botDiv {
        position: fixed;
        bottom: 0;
        right: 0;
        z-index: 10000;
        display: none;
        width: 400px;
        height: 450px;
        box-sizing: border-box;
        margin: 0 !important;
        padding: 0 !important;
      }
      
      .botDiv a {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 10001;
        margin: 0 !important;
        padding: 0 !important;
        pointer-events: auto;
      }
      
      .botDiv iframe {
        margin: 0 !important;
        padding: 0 !important;
        display: block !important;
        width: 100% !important;
        height: 100% !important;
      }
    </style>
  </head>
  <body>
      <header>
      <div id="particles-js" class="jumbotron0">
          <!-- scripts -->
        <script src="js/particles.js"></script>
        <script src="js/app.js"></script>
<script language="javascript">
    // Real-time validation
    function validateName() {
        const nameInput = document.getElementById("fullName");
        const nameHelp = document.getElementById("nameHelp");
        const name = nameInput.value.trim();
        
        if (name.length === 0) {
            showError(nameInput, nameHelp, "Please enter your name.");
            return false;
        } else if (name.length < 2) {
            showError(nameInput, nameHelp, "Name must be at least 2 characters.");
            return false;
        } else if (!/^[a-zA-Z\s\-'\.]+$/.test(name)) {
            showError(nameInput, nameHelp, "Name can only contain letters, spaces, hyphens, and apostrophes.");
            return false;
        }
        
        showSuccess(nameInput, nameHelp);
        return true;
    }
    
    function validateEmail() {
        const emailInput = document.getElementById("email");
        const emailHelp = document.getElementById("emailHelp");
        const email = emailInput.value.trim();
        
        if (email.length === 0) {
            showError(emailInput, emailHelp, "Please enter your email address.");
            return false;
        }
        
        // RFC 5322 simplified email regex
        const emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        
        if (!emailRegex.test(email)) {
            showError(emailInput, emailHelp, "Please enter a valid email address (e.g., name@example.com).");
            return false;
        }
        
        showSuccess(emailInput, emailHelp);
        return true;
    }
    
    function validateMessage() {
        const msgInput = document.getElementById("msg");
        const msgHelp = document.getElementById("messageHelp");
        const message = msgInput.value.trim();
        
        if (message.length === 0) {
            showError(msgInput, msgHelp, "Please enter a message.");
            return false;
        } else if (message.length < 10) {
            showError(msgInput, msgHelp, "Message must be at least 10 characters.");
            return false;
        } else if (message.length > 1000) {
            showError(msgInput, msgHelp, "Message must be less than 1000 characters.");
            return false;
        }
        
        showSuccess(msgInput, msgHelp);
        return true;
    }
    
    function showError(input, helpText, message) {
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        helpText.textContent = message;
        helpText.style.display = "block";
        helpText.style.color = "#dc3545";
    }
    
    function showSuccess(input, helpText) {
        input.classList.remove("is-invalid");
        input.classList.add("is-valid");
        helpText.style.display = "none";
    }
    
    function checkMoat(event)
        {
            // Validate all fields
            const isNameValid = validateName();
            const isEmailValid = validateEmail();
            const isMessageValid = validateMessage();
            
            // Check if all validations passed
            if (!isNameValid || !isEmailValid || !isMessageValid) {
                if (event) event.preventDefault();
                return false;
            }
            
            // Bot prevention check
            if(document.getElementById("moat").value == "" )
                {
                    // Validation passed and no bot detected - allow form to submit normally
                    return true;
                }
            else
            {
                // Bot detected
                if (event) event.preventDefault();
                alert("thwarted");
                return false;
            }
        }
        
    // Add real-time validation listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById("fullName").addEventListener("blur", validateName);
        document.getElementById("email").addEventListener("blur", validateEmail);
        document.getElementById("msg").addEventListener("blur", validateMessage);
        
        // Also validate on input for better UX
        document.getElementById("fullName").addEventListener("input", function() {
            if (this.classList.contains("is-invalid")) validateName();
        });
        document.getElementById("email").addEventListener("input", function() {
            if (this.classList.contains("is-invalid")) validateEmail();
        });
        document.getElementById("msg").addEventListener("input", function() {
            if (this.classList.contains("is-invalid")) validateMessage();
        });
    });
          
    function spawnChat()
        {
            
            document.getElementById("intercom").style.display="none";
            document.getElementById("botDiv").style.display="block";
            //alert(document.getElementById("botDiv").style.display);
        }
    
    function closeChat()
        {
            
            document.getElementById("botDiv").style.display="none";
            document.getElementById("intercom").style.display="block";
            //alert(document.getElementById("botDiv").style.display);
        }    
          </script>          
          
      </div>                    
    </header>
<?php include "navi.php"; ?>
    <section>
      <div class="container main-content">
      <div class="col-12" style="padding-top:12em;">
              <h2 class="text-center">
                    Advanced Additive Manufacturing & 3D Scanning</h2>
              <p>Bioprinting and 3D Scanning are transformative technologies that enable precise fabrication and digital capture of complex geometries and biological structures.<br>
                <br>
                At Brown Bioprinting & Imaging, we deliver cutting-edge additive manufacturing solutions for medical device development, precision prototyping, and advanced research applications.
              <p>We are a licensed, insured advanced manufacturing services provider serving clients in Charlotte North Carolina, and the world.&nbsp; We specialize in high-resolution 3D bioprinting, precision scanning, and digital reconstruction using state-of-the-art equipment and proprietary processes for production systems.&nbsp; All employees are US Citizens.&nbsp;🇺🇸 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
<p>&nbsp;</p>
              <p class="text-center">
                 <a class="btn btn-primary btn-sm" href="#verticals" role="button" id="learnMoreHome">Verticals</a>&nbsp;&nbsp;<a class="btn btn-primary btn-sm" href="#shockUs" role="button" id="contactHome">Contact Us</a>
<!--                  <A href="#" onClick="$('#learnMore').animatescroll();" >Learn More</A>-->
             </p>
      </div>
      </div>
      </div>
      </div>
      <div class="container">
        <div class="container">
        <div class="row verticals" style="padding-top:55em;"> <a name="verticals"></a>
          <div class="col-lg-4 col-md-6 col-sm-12 text-center">
              <a href="#medici">
<!--            <img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/iconUltrasound.svg" title="Created by Iconic from Noun Project" data-holder-rendered="true">-->
            <h3>Medical Devices</h3></a>
            <p>&nbsp;</p>
          </div>
            <div class="col-lg-4 col-md-6 col-sm-12 text-center">
                <a href="#finis">
<!--            <img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/iconFinancialServices.svg" title="from the Noun ProjectCreated by Siipkan Creative" data-holder-rendered="true" >-->
                    <h3>Precision Prototyping</h3></a>
            <p>&nbsp;&nbsp;</p>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-12 text-center"><a href="#energos">
<!--           <img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/iconEnergyProducers.svg" title="Created by Vectors Point from the Noun Project" data-holder-rendered="true">-->
              <h3>Research & Materials<sup></sup></h3></a>
          </div>
          </div> 
          <div class="row">
           <!-- <div class="col-sm-4 col-auto mx-auto"> <a class="btn btn-block btn-lg btn-success" href="#" title="">Sign up now!</a> </div>-->
          </div>
        </div>
      </div>
      <div class="container" style="padding-top:20em;">
        <div class="row"><a name="medici"></a>
          <div class="col-12 mb-2 text-center">
               <img class="rounded-circle" alt="140x140" style="width: 70px; height: 70px;" src="images/iconMedici.svg" title="Created by Sergey Demushkin from the Noun Project" data-holder-rendered="true">
            <h2>Medical Devices&nbsp;</h2>
          </div>
        </div>
<div class="row">
	<div class="col-lg-4 col-md-6 col-sm-12 text-center"></div>
	<div class="col-lg-4 col-md-6 col-sm-12 text-center"><a name="noteRisk"><img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/notesProcessing.svg" title="Bioprinting Services" data-holder-rendered="true"></a><h3>High-Resolution Bioprinting</h3>
<h2 style="margin-top:80px;">Precision Engineered Biological Structures</h2>
<p>Utilizing advanced extrusion and inkjet bioprinting technologies to create complex tissue scaffolds and personalized medical devices.</p>

<div style="background-color: rgba(240, 237, 235, 0.75); border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding:25px; margin-bottom: 10px;">
  <p><strong>Our Medical Device Services Include:</strong></p>
  <ul style="text-align: left; display: inline-block;">
    <li>Customized implant design and fabrication</li>
    <li>Biocompatible scaffold development</li>
    <li>Tissue engineering support</li>
    <li>Regulatory compliance documentation</li>
    <li>Small batch to pilot production runs</li>
  </ul>
  <p style="margin-top:15px;"><a href="#shockUs" style="background:#005EB8;color:white;padding:12px 24px;text-decoration:none;border-radius:8px;font-weight:bold;">Request a Quote →</a></p>
</div>
	</div>
		<div class="col-lg-4 col-md-6 col-sm-12 text-center"></div>
		  </div>
<div class="row">
          <div class="col-lg-4 col-md-6 col-sm-12 text-center">
            <img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/iconUltrasound.svg" title="3D Scanning" data-holder-rendered="true" >
            <h3>3D Scanning Services</h3>
            <p>High-fidelity capture of physical objects into digital CAD models. Ideal for reverse engineering and design documentation.</p>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-12 text-center">
            <img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/iconCloudUpload.svg" title="Digital Archive" data-holder-rendered="true">
            <h3>Digital Archive</h3>
            <p>Secure cloud storage and version control of all scan data, CAD models, and build files with full compliance documentation.</p>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-12 text-center">
            <img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/iconAI.svg" title="Design Optimization" data-holder-rendered="true">
            <h3>Design Optimization</h3>
            <p>Advanced computational analysis to optimize part geometry, material efficiency, and printability before production.</p>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-12 text-center">
            <img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/iconFormvana.svg" title="Quality Control" data-holder-rendered="true">
            <h3>Quality Control & Testing</h3>
            <p>Post-print inspection, dimensional verification, and material testing to ensure specifications are met.</p>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-12 text-center">
            <img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/iconPersonaLP.svg" title="Regulatory Support" data-holder-rendered="true">
            <h3>Regulatory Support</h3>
            <p>Full documentation packages and FDA/CE compliance support for medical device submissions and certifications.</p>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-12 text-center">
            <img class="rounded-circle" alt="140x140" style="width: 140px; height: 140px;" src="images/iconPersonaMD.svg" title="Consultation" data-holder-rendered="true">
            <h3>Expert Consultation</h3>
            <p>Direct collaboration with our manufacturing engineers to solve complex design and production challenges.</p>
          </div>
        </div>
      </div>
    
    
    <div class="container">
        <div class="row"><a name="finis"></a>
          <div class="col-12 mb-2 text-center">
            <img class="rounded-circle" alt="140x140" style="width: 70px; height: 70px;" src="images/iconFinancialServices.svg" title="Precision Prototyping" data-holder-rendered="true">
            <h2>Precision Prototyping</h2>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6 col-lg-4">
            <h3>Rapid Prototyping</h3>
            <p>Fast turnaround from concept to physical prototype. Iterate quickly on product designs using multiple materials and printing technologies to find optimal solutions.</p>
            <p>&nbsp;</p>
          </div>
          <div class="col-sm-6 col-lg-4">
            <h3>Complex Geometry Production</h3>
            <p>Manufacture intricate internal structures and geometries impossible with traditional manufacturing. Perfect for aerospace, automotive, and industrial applications requiring lightweight, high-strength components.</p>
          </div>
          <div class="col-sm-6 col-lg-4">
            <h3>Multi-Material Assembly</h3>
            <p>Combine different materials in single print jobs to create assemblies with varying properties and integrated functional elements.</p>
          </div>
        </div>
      </div>
    
    
        <div class="container">
        <div class="row"><a name="energos"></a>
          <div class="col-12 mb-2 text-center">
           <img class="rounded-circle" alt="140x140" style="width: 70px; height: 70px;" src="images/iconEnergyProducers.svg" title="Research & Materials" data-holder-rendered="true">
            <h2>Research & Materials</h2>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6 col-lg-6">
            <h3>Materials Research & Development</h3>
            <p>Collaborate on developing and validating new bioink formulations, scaffolding materials, and composite systems. Small batch production for testing and optimization of novel materials.</p>
<!--            <p><a class="btn btn-link" href="https://www.adobe.com">View details »</a></p>-->
          </div>
          <div class="col-sm-6 col-lg-6">
            <h3>Academic & Institutional Partnerships</h3>
            <p>Support research institutions with production capabilities, equipment access, and technical expertise for groundbreaking projects in regenerative medicine and advanced manufacturing.</p>
<!--            <p><a class="btn btn-link" href="https://www.adobe.com">View details »</a></p>-->
          </div>
<!--
          <div class="col-sm-6 col-lg-4">
            <h3>Energy Usage Pattern AI</h3>
            <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis, adipisci recusandae veniam laudantium distinctio temporibus eveniet dolorum earum iusto veritatis provident ducimus minima dolore quas vel omnis cumque voluptas quibusdam.</p>
            <p><a class="btn btn-link" href="https://www.adobe.com">View details »</a></p>
          </div>
-->
        </div>
      </div>

    </section>
    
    <div class="container" style="position: relative; z-index: 10; padding: 0; margin: 0 auto;">
      <div class="row"><a name="shockUs"></a>
        <div class="col-12 col-md-8 mx-auto">
          <div class="jumbotron" style="background-color: rgba(240, 237, 235, 0.75); border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding-top: 1.5rem;">
            <div class="row text-center">
              <div class="text-center col-12" style="margin-bottom: 0.5rem;">
                <img src="images/bbiLogoLite.svg" alt="Brown BI Logo" style="height:64px; margin-bottom:1rem;">
                <h2 style="margin-bottom: 0.5rem;">Request Information</h2><a name="contactForm"/>
				  <p style="text-align: center; margin-bottom: 0.5rem;">Best Phone: 01:704.609.4500 or <a href="mailto:info@brownbi.com">info@brownbi.com</a></p>
              </div>
              <div class="text-center col-12" id="learnMore" style="margin-top: 0;">
                <!-- CONTACT FORM https://github.com/jonmbake/bootstrap3-contact-form -->
                <form id="shockRats" name="shockRats" class="text-center" method="post" action="form/contactscript2.php" onsubmit="return checkMoat(event);" novalidate>
                  <div class="form-group">
                    <label for="fullName">Your Name <span class="text-danger">*</span></label>
                    <input name="name" type="text" required="required" class="form-control" id="fullName" placeholder="John Doe" autocomplete="name" aria-describedby="nameHelp" minlength="2" maxlength="100">
                    <span id="nameHelp" class="form-text" style="display: none;">Please enter your name.</span>
                  </div>
                  <div class="form-group">
                    <label for="email">Best E-Mail <span class="text-danger">*</span></label>
                    <input name="email" type="email" required="required" class="form-control" id="email" placeholder="john@example.com" autocomplete="email" aria-describedby="emailHelp">
                    <span id="emailHelp" class="form-text" style="display: none;">Please enter a valid e-mail address.</span>
                  </div>
                    <!--BotPrevention-->
                    <div class="form-group" style="display:none" aria-hidden="true">
                    <label for="moat">Moat</label>
                    <input name="moat" type="text" class="form-control" id="moat" placeholder="Email Address" aria-describedby="moatHelp" tabindex="-1" autocomplete="off">
                    <span id="moatHelp" class="form-text text-muted" style="display: none;">Please enter a valid moat value.</span>
                  </div>
                    <!--/BotPrevention-->
                  <div class="form-group">
                    <label for="msg">Message <span class="text-danger">*</span></label>
                    <textarea name="msg" cols="100" rows="6" required="required" class="form-control" id="msg" placeholder="Tell us about your bioprinting and 3D scanning needs..." aria-describedby="messageHelp" minlength="10" maxlength="1000"></textarea>
                    <span id="messageHelp" class="form-text" style="display: none;">Please enter a message.</span>
                    <small class="form-text text-muted" style="display: block; margin-top: 5px;">Minimum 10 characters</small>
                  </div>
                  
                  <!-- Math CAPTCHA -->
                  <?php
                    $a = rand(1, 9);
                    $b = rand(1, 9);
                    $_SESSION['captcha_answer'] = $a + $b;
                  ?>
                  <div class="form-group">
                    <label for="captcha">What is <?php echo $a; ?> + <?php echo $b; ?>? <span class="text-danger">*</span></label>
                    <input type="text" name="captcha" id="captcha" class="form-control" required autocomplete="off" maxlength="2" pattern="\d+">
                    <span id="captchaHelp" class="form-text" style="display: none;">Please answer the math question.</span>
                  </div>
                  
                  <!-- Hidden Checkbox Anti-Bot -->
                  <input type="checkbox" name="human_check" value="1" style="position:absolute; left:-9999px;" checked>
                  
                  <button type="submit" id="feedbackSubmit" class="btn btn-primary btn-sm">
                    <span id="submitText">Send Message</span>
                    <span id="submitSpinner" style="display: none;">
                      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                      Sending...
                    </span>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <footer class="text-center">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <p>Copyright 2026 © Brown Bioprinting & Imaging, LLC. All rights reserved.</p>
          </div>
        </div>
          <div class="intercom" id="intercom" ><a href="#" onclick="spawnChat()"><img src="images/iconChat.svg"></a></div>
  <div class="botDiv" id="botDiv"><a href="#" onClick="closeChat()" style="float: right"><img src="images/iconClose.svg" height="24px" width="24px"></a>
      <iframe src='chat.php' style='border: none; width: 100%; height: 100%;'></iframe>
          </div>
      </div>
    </footer>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="js/jquery-3.4.1.min.js"></script> 
    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="js/popper.min.js"></script> 
    <script src="js/bootstrap-4.4.1.js"></script>
  </body>
</html>