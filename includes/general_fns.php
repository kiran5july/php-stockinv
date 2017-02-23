<?php
  Function cleanFormInput($value) {
     $value = Trim(strip_tags($value));
     return $value;
  }

  Function encryptData($m) {
     global $encryptKey;

     $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_BLOWFISH,MCRYPT_MODE_CBC),MCRYPT_RAND);
     $c = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryptKey, $m, MCRYPT_MODE_CBC, $iv);
     // encode and tack on the iv
     $c1 = base64_encode($c . "\$IV\$" . $iv);
     return $c1;
  }

  Function unEncryptData($c) {
     global $encryptKey;

     // decode and get the iv off
     list($c1,$iv)=explode("\$IV\$",base64_decode($c));
     $m = mcrypt_decrypt(MCRYPT_BLOWFISH,$encryptKey,$c1,MCRYPT_MODE_CBC,$iv);
     return rtrim($m);
  }

  Function writeNA($value) {
      If ($value) {
          return $value;
      } Else {
          return "N/A";
      }
  }

  Function writeStatus($statLetter) {
      If ($statLetter == "w") {
          Return "<font color='green'>Working</font>";
      } ElseIf ($statLetter == "i") {
          Return "<font color='cc6600'>In Service</font>";
      } ElseIf ($statLetter == "n") {
          Return "<font color='red'>Needs Service</font>";
      }
  }

  /* To use paging, first call determinePageNumber, passing it the sql select
     statement you intend to ultimately build off of. After you have done that, 
     execute the query as normal.

     At the end of where you display the results of the query, call createPaging, 
     which will build the paging nav. Also note: assuming your result set is 
     built in part from user input via a form, make sure the form is GET, not POST.

     Before you call determinePageNumber, you may optionally choose to (globally) 
     set $rowLimit equal to some number other than 30, which is the default 
     record limit per page.
  */

  Function determinePageNumber($strSQL) {
      global $rowLimit, $rowOffset, $pageNumber;

      If (!$rowLimit) {
          $rowLimit = 30;
      }
      if (!$rowOffset) {
          $rowOffset = 0; 
      }
      $result = mysql_query($strSQL);
      $numrec = mysql_num_rows($result);
      $pageNumber = intval($numrec/$rowLimit);
      if ($numrec%$rowLimit) $pageNumber++; // add one page if remainder

      $strSQL .= " LIMIT $rowOffset, $rowLimit";
      Return $strSQL;

      # would be nice to return recordset in future...
      # result=mysql_query("select * from tablename $query_where limit $rowOffset,$rowLimit");
  }

  Function createPaging($qsParamToRemove="") {
    global $rowLimit, $rowOffset, $pageNumber, $QUERY_STRING;
    If (strpos($QUERY_STRING, "owOffset")) {
        $posQSMinusOffset = strpos($QUERY_STRING, "&")+1;
        $qstring = substr($QUERY_STRING, $posQSMinusOffset);
    } Else {
        $qstring = $QUERY_STRING;
    }

    If ($qsParamToRemove) { # need to make this capable of taking arrays someday.
        # $stringToFind = substr($qsParamToRemove, 1);
        # If (strpos($qstring, $stringToFind)) {
            $pattern = "/".$qsParamToRemove."[\045|\w|\075]*[\046]?/";
            $qstring = preg_replace($pattern, "", $qstring);
        # }
    }
      
    if ($pageNumber>1) {
      echo "<TABLE CELLPADDING=0 BORDER=0 CELLSPACING=5 WIDTH=100%><TR><TD>";
          if ($rowOffset>=$rowLimit) {
              $newoff=$rowOffset-$rowLimit;
              
              echo "<A HREF=\"$PHP_SELF?rowOffset=$newoff&$qstring\">&lt;-- PREV</A> ";
          } else {
              echo "&lt;-- PREV ";
          }
  
          echo " &nbsp; ";
  
          for ($i=1;$i<=$pageNumber;$i++) {
              if ((($i-1)*$rowLimit)==$rowOffset) {
                  echo "$i ";
              } else {
                  #if (($i < 8) OR ($i > ($pageNumber - 8))) {
                       $newoff=($i-1)*$rowLimit;
                       echo " <A HREF=\"$PHP_SELF?rowOffset=$newoff&$qstring\">$i</A> ";
                  #} elseif (!$wroteDots) {
                  #     echo " ... ";
                  #     $wroteDots = TRUE;
                  #}
              }
          }
          echo "&nbsp; ";
          if ($rowOffset!=$rowLimit*($pageNumber-1)) {
              $newoff=$rowOffset+$rowLimit;
              echo "<A HREF=\"$PHP_SELF?rowOffset=$newoff&$qstring\">NEXT--&gt;</A> ";
          }else{
              echo "NEXT--&gt; ";
          }
          echo "</TD></TR></TABLE>";
    }
  }

  # use this to set the class tag of alternating rows in tables (in conjunction with stylesheet)
  Function alternateRowColor() {
      global $rowStyle;
      $rowStyle ++;
      If ($rowStyle%2 == 1) {
           Return "row1";
      } Else {
           Return "row2";
      }
  }

  Function formatForBrowser($strIE, $strElse) {
      global $HTTP_USER_AGENT;
      If (strpos($HTTP_USER_AGENT, "MSIE")) {
           echo $strIE;
      } Else {
           echo $strElse;
      }
  }

  Function getPageName() {
      global $PHP_SELF;
      $returnString = strrchr($PHP_SELF, "/");
      $returnString = substr($returnString, 1);
      Return $returnString;
  }

  Function makeHomeURL($stringToRemove = "") {
      global $SERVER_NAME, $PHP_SELF;
      $strURL = $SERVER_NAME.$PHP_SELF;
      If ($stringToRemove != "") {
         $intPos = strpos($strURL, $stringToRemove);
         $strURL = substr($strURL, 0, ($intPos-1));
      }
If(!$strURL) $strURL="localhost/pmi281";
      Return $strURL;
  }

  Function buildName($strFirstName, $strMiddleName, $strLastName, $intShowType="") {
      If ($strMiddleName) {
           If ($intShowType == 1) {
                $strFullName = $strFirstName." ".$strMiddleName." ".$strLastName;
           } Else {
                $strFullName = $strLastName.", ".$strFirstName." ".$strMiddleName;
           }
      } Else {
           If ($intShowType == 1) {
                $strFullName = $strFirstName." ".$strLastName;
           } Else {
                $strFullName = $strLastName.", ".$strFirstName;
           }
      }
      Return $strFullName;
  }

  Function urlSafe($strQueryString) {
      $strQueryString = urlencode($strQueryString);
      $strQueryString = str_replace("%26", "&", $strQueryString);
      $strQueryString = str_replace("%3D", "=", $strQueryString);
      Return $strQueryString;
  }

  Function redirect($strURL, $strQueryString = "") {
      $strQueryString = urlSafe($strQueryString);
      header ("Location: $strURL?$strQueryString");
      header ("QUERY_STRING: $strQueryString");
      exit;
  }

  Function writeSelected($SelectValue,$OurValue) {
      If ($SelectValue==$OurValue) {
          Return "selected";
      }
  }

  Function writeChecked($SelectValue,$OurValue) {
      If ($SelectValue==$OurValue) {
          Return "checked";
      }
  }

  Function makeNull($val, $includeSingleQuotes = "") {
      If ($val == "" OR $val == "00/00/0000") {
           return "NULL";
      } Else {
           If ($includeSingleQuotes) {
               return "'".$val."'";
           } Else {
               return $val;
           }
      }
  }

  Function antiSlash($strValue) {
      If ($strValue != "") {
          $strValue = stripslashes($strValue);
          $strValue = str_replace("\"", "&quot;", $strValue); # fixes broken html input field problem
      }
      Return $strValue;
  }

  Function fillError($strValue) {
      global $strError;
      If ($strError == "") {
           $strError = $strValue;
      }
  }

  Function validateText($strFieldName, $strValidate, $intMin, $intMax, $bolRequired, $bolHTML) {
      global $strError;
      If ($bolHTML == FALSE) {
          $strValidate = Trim(strip_tags($strValidate));
      } Else {
          $strValidate = Trim($strValidate);
      }
      If ($bolRequired == TRUE OR $strValidate != "") {
          If ($strValidate=="") {
              fillError("$strFieldName is required.");
              Return $strValidate;
          } Else {
              $intField = strlen($strValidate);
              If (($intField >= $intMin) AND ($intField <= $intMax)) {
                  Return $strValidate;
              } Else {
                  If ($intMin==$intMax) {
                       fillError("$strFieldName must be exactly $intMax characters long.");
                       Return $strValidate;
                  } Else {
                       fillError("$strFieldName must be between $intMin and $intMax characters in length.");
                       Return $strValidate;
                  }
              }
          }
      } Else {
          Return $strValidate;
      }
  }

  Function validateChoice($strFieldName, $strValidate) {
      global $strError;
      $strValidate = strip_tags($strValidate);
      If ($strValidate == "") {
           fillError("$strFieldName is required.");
      } Else {
           Return $strValidate;
      }
  }

  Function validateEmail($strFieldName, $strValidate, $bolRequired) {
      global $strError;
      $strValidate = trim(strtolower(strip_tags($strValidate)));

      If ($bolRequired == TRUE OR $strValidate != "") {
          If ($strValidate=="") {
              fillError("$strFieldName is required.");
              Return $strValidate;
          } Else {
              $Pos = strpos($strValidate, "@", 1);
              If ($Pos===FALSE) {
                  fillError("$strFieldName is not in the correct format.");
                  Return $strValidate;
              } Else {
                  $Pos2 = strpos($strValidate, ".", ($Pos+2));
                  If ($Pos2===FALSE) {
                      fillError("$strFieldName is not in the correct format.");
                      Return $strValidate;

                   } Else {
                       $intField = strlen($strValidate);
                       If ($intField>60) {
                            fillError("$strFieldName may not be more than 60 characters long.");
                            Return $strValidate;
                        } Else {
                            Return $strValidate;
                        }
                   }
              }
          }
      } Else {
          Return $strValidate;
      }
  }

  Function validateNumber($strFieldName, $strValidate, $intMin, $intMax, $bolRequired) {
      global $strError;
      $strValidate = Trim(strip_tags($strValidate));
      $strValidate = str_replace(" ", "", $strValidate);
      $strValidate = str_replace(")", "", $strValidate);
      $strValidate = str_replace("(", "", $strValidate);
      $strValidate = str_replace("-", "", $strValidate);

      If ($bolRequired == TRUE OR $strValidate != "") {
          If ($strValidate=="") {
              fillError("$strFieldName is required.");
              Return $strValidate;
          } Else {
              $intField = strlen($strValidate);
              If (($intField >= $intMin) AND ($intField <= $intMax)) {
                  If (is_numeric($strValidate)===TRUE) {
                       Return $strValidate;
                  } Else {
                       fillError("$strFieldName must be purely numeric.");
                       Return $strValidate;
                  }
              } Else {
                  If ($intMin==$intMax) {
                       fillError("$strFieldName must be exactly $intMax digits long.");
                       Return $strValidate;
                  } Else {
                       fillError("$strFieldName must be between $intMin and $intMax digits in length.");
                       Return $strValidate;
                  }
              }
          }
      } Else {
          Return $strValidate;
      }
  }

  Function validateExactNumber($strFieldName, $strValidate, $intMin, $intMax, $bolRequired, $intDecimals="") {
      global $strError;
      $strValidate = Trim(strip_tags($strValidate));
      If ($bolRequired == TRUE OR $strValidate != "") {
          If ($strValidate == "") {
              fillError("$strFieldName is required.");
          } ElseIf (is_numeric($strValidate) === FALSE) {
              fillError("$strFieldName must be purely numeric.");
          } ElseIf (($strValidate < $intMin) OR ($strValidate > $intMax)) {
              fillError("$strFieldName must be between $intMin and $intMax.");
           # } ElseIf (strstr($strValidate, ".")) {
           #     If strstr(strstr($strValidate, "."), ".")
           #     fillError("too many decimals...");
          } ElseIf ($intDecimals !== "") {
              $decimalPlaces = strlen(strstr($strValidate, "."));
              If ($decimalPlaces > ($intDecimals+1)) {
                  If ($intDecimals == 0) {
                      fillError("$strFieldName must be a whole number between $intMin and $intMax.");
                  } Else {
                      fillError("$strFieldName may have no more than $intDecimals digits after the decimal.");
                  }
              } ElseIf ($intDecimals == 0) {
                  $strValidate = round($strValidate);
              }
          }
      }
      Return $strValidate;
  }

  Function validateIP($fieldSuffix, $bolRequired, $formType="POST", $requireAllParts=TRUE) {
      global $strError, $HTTP_POST_VARS;

      $ip1  = "txtIP1".$fieldSuffix;
      $ip2  = "txtIP2".$fieldSuffix;
      $ip3  = "txtIP3".$fieldSuffix;
      $ip4  = "txtIP4".$fieldSuffix;

      If ($formType == "GET") {
          global $HTTP_GET_VARS;
          $ip1  = Trim(strip_tags($HTTP_GET_VARS[$ip1]));
          $ip2  = Trim(strip_tags($HTTP_GET_VARS[$ip2]));
          $ip3  = Trim(strip_tags($HTTP_GET_VARS[$ip3]));
          $ip4  = Trim(strip_tags($HTTP_GET_VARS[$ip4]));
      } Else {
          $ip1  = Trim(strip_tags($HTTP_POST_VARS[$ip1]));
          $ip2  = Trim(strip_tags($HTTP_POST_VARS[$ip2]));
          $ip3  = Trim(strip_tags($HTTP_POST_VARS[$ip3]));
          $ip4  = Trim(strip_tags($HTTP_POST_VARS[$ip4]));
      }

      $ip1 = str_replace(".", "", $ip1);
      $ip2 = str_replace(".", "", $ip2);
      $ip3 = str_replace(".", "", $ip3);
      $ip4 = str_replace(".", "", $ip4);

      If ($bolRequired OR (($ip1 OR $ip2 OR $ip3 OR $ip4) AND $requireAllParts)) {
          $ipRequired = TRUE;
      }

      If ($ipRequired AND (($ip1=="") OR ($ip2=="") OR ($ip3=="") OR ($ip4==""))) {
          fillError("Please specify <u>all</u> parts of the IP Address.");
      }

      $strIP1  = validateExactNumber("Each part of the IP Address", $ip1, 0, 255, $ipRequired, 0);
      $strIP2  = validateExactNumber("Each part of the IP Address", $ip2, 0, 255, $ipRequired, 0);
      $strIP3  = validateExactNumber("Each part of the IP Address", $ip3, 0, 255, $ipRequired, 0);
      $strIP4  = validateExactNumber("Each part of the IP Address", $ip4, 0, 255, $ipRequired, 0);

      If ($strIP1 OR $strIP2 OR $strIP3 OR $strIP4) {
          return $strIP1.".".$strIP2.".".$strIP3.".".$strIP4;
      } Else {
          return "";
      }
  }

  Function buildIP($value, $fieldSuffix) {
     If ($value) {
         $dot1 = strpos($value, ".", 0);
         $dot2 = strpos($value, ".", ($dot1+1));
         $dot3 = strpos($value, ".", ($dot2+1));
     }

     $strIP1 = substr($value, 0, $dot1);
     $strIP2 = substr($value, ($dot1+1), (($dot2-$dot1)-1));
     $strIP3 = substr($value, ($dot2+1), (($dot3-$dot2)-1));
     $strIP4 = substr($value, ($dot3+1));
?>
     <input type='text' name='txtIP1<?=$fieldSuffix; ?>' value='<?=$strIP1; ?>' size='3' maxlength='3'> <b>.</b> 
     <input type='text' name='txtIP2<?=$fieldSuffix; ?>' value='<?=$strIP2; ?>' size='3' maxlength='3'> <b>.</b>
     <input type='text' name='txtIP3<?=$fieldSuffix; ?>' value='<?=$strIP3; ?>' size='3' maxlength='3'> <b>.</b>
     <input type='text' name='txtIP4<?=$fieldSuffix; ?>' value='<?=$strIP4; ?>' size='3' maxlength='3'>
<?php
  }

  Function buildPhone($varNameSuffix, $phoneVal) {
      If ($phoneVal != "") {
         $phone1 = substr($phoneVal, 0, 3);
         $phone2 = substr($phoneVal, 3, 3);
         $phone3 = substr($phoneVal, 6, 4);
      }
      echo "( <input size='3' maxlength='3' type='text' name='txtPhone1".$varNameSuffix."' value='$phone1'> ) ";
      echo "<input size='3' maxlength='3' type='text' name='txtPhone2".$varNameSuffix."' value='$phone2'> - ";
      echo "<input size='4' maxlength='4' type='text' name='txtPhone3".$varNameSuffix."' value='$phone3'>\n";
  }

  Function validatePhone($strFieldName, $varNameSuffix, $bolRequired) {
      global $strError, $HTTP_POST_VARS;

      $phone1 = "txtPhone1".$varNameSuffix;
      $phone2 = "txtPhone2".$varNameSuffix;
      $phone3 = "txtPhone3".$varNameSuffix;
      $phone1 = Trim(strip_tags($HTTP_POST_VARS[$phone1]));
      $phone2 = Trim(strip_tags($HTTP_POST_VARS[$phone2]));
      $phone3 = Trim(strip_tags($HTTP_POST_VARS[$phone3]));

	  $phoneVal = $phone1.$phone2.$phone3;

      If ($phoneVal != "") { 
         If (is_numeric($phoneVal)===TRUE) {
             $phoneLen = strlen($phoneVal); 
             If ($phoneLen != 10) {
                 fillError("$strFieldName is missing digits.");
                 Return $phoneVal;              
             } Else {
                 Return $phoneVal;
             }
         } Else {
            fillError("$strFieldName must be completely numeric.");
            Return $phoneVal;
         }
     } ElseIf ($bolRequired) {
         fillError("$strFieldName is required.");
         Return $phoneVal;
     } 
  }

  # For use when comparing a "user-formatted date" (mm/dd/yyyy) with a date in the db
  ## Note - you don't need this to insert a date into the db - for that, use: date("Ymd", $dateVal);
  Function validateDate($fieldName, $dateVal, $intMin, $intMax, $bolRequired) {
      global $strError;

      If ($dateVal == "mm/dd/yyyy") {
            $dateVal = "";
      }

      If ($dateVal != "") {
            $dateVal = str_replace(".", "/", $dateVal);
            $dateVal = str_replace("-", "/", $dateVal);
            $tempDate = $dateVal;
            $tempDate = str_replace("/", "", $tempDate);

            If (is_numeric($tempDate)) {
                  $intLoc = strpos($dateVal, "/");
                  If ($intLoc == 2) {
                      $strMonth = substr($dateVal, 0, 2);
                  } ElseIf ($intLoc == 1) {
                      $strMonth = "0".substr($dateVal, 0, 1);
                  } Else {
                      fillError("$fieldName was not input in a valid format.");
                      Return "$dateVal";
                  }

                  $intLoc2 = strpos($dateVal, "/", ($intLoc + 1));
                  If ($intLoc2 == 4 AND $intLoc == 1) {
                      $strDay = substr($dateVal, 2, 2);
                  } ElseIf ($intLoc2 == 5) {
                      $strDay = substr($dateVal, 3, 2);
                  } ElseIf ($intLoc2 == 3) {
                      $strDay = "0".substr($dateVal, 2, 1);
                  } Else {
                      fillError("$fieldName was not input in a valid format.");
                      Return "$dateVal";
                  }

                  $strYear = substr($dateVal, ($intLoc2+1), 4);
                  If (strlen($strYear) != 4) {
                       fillError("$fieldName requires a four-digit year.");
                       Return "$dateVal";
                  } ElseIf (($strYear > $intMax) OR ($strYear < $intMin)) {
                       fillError("$fieldName cannot be before $intMin or later than $intMax.");
                       Return "$dateVal";
                  }

                  Return $dateVal;
            } Else {
                  fillError("$fieldName must not contain letters or symbols (aside from /).");
                  Return "$dateVal";
            }
      } ElseIf ($bolRequired == TRUE) {
            fillError("$fieldName is required.");
            Return "";
      }
  }

  # For use when retrieving a date from the db to display
  Function displayDate($dateVal) {
      If ($dateVal) {
            $dateVal  = str_replace("-", "", $dateVal);
            $strDay   = substr($dateVal, 6, 2);
            $strMonth = substr($dateVal, 4, 2);
            $strYear  = substr($dateVal, 0, 4);
            $dateVal  = "$strMonth/$strDay/$strYear";
      }
      Return $dateVal;
  }

  # For use when retrieving a date from the db to display
  Function displayDateTime($dateVal) {
      If ($dateVal) {
            $dateVal   = str_replace("-", "", $dateVal);
            $strDay    = substr($dateVal, 6, 2);
            $strMonth  = substr($dateVal, 4, 2);
            $strYear   = substr($dateVal, 0, 4);

            $strHour   = substr($dateVal, 9, 2);
            $strMinute = substr($dateVal, 12, 2);
            $dateVal   = "$strMonth/$strDay/$strYear, $strHour:$strMinute";
      }
      Return $dateVal;
  }

  # For inserting or updating 'mm/dd/yyyy' dates in db, or comparing with dates in the db.
  # Returns a string, "NULL", if $dateVal is empty.
  Function dbDate($dateVal) {
      $dateVal = makeNull($dateVal, FALSE);
      If ($dateVal != "" AND $dateVal != "NULL") {
          $intLoc = strpos($dateVal, "/");
          If ($intLoc == 2) {
              $strMonth = substr($dateVal, 0, 2);
          } Else {
              $strMonth = "0".substr($dateVal, 0, 1);
          }

          $intLoc2 = strpos($dateVal, "/", ($intLoc + 1));
          If ($intLoc2 == 4 AND $intLoc == 1) {
              $strDay = substr($dateVal, 2, 2);
          } ElseIf ($intLoc2 == 5) {
              $strDay = substr($dateVal, 3, 2);
          } Else {
              $strDay = "0".substr($dateVal, 2, 1);
          }

          $strYear = substr($dateVal, ($intLoc2+1), 4);
          $dateVal = "'".$strYear.$strMonth.$strDay."'";
      }
      Return $dateVal;
  }

  Function buildDate($fieldName, $dateVal) {
      If ($dateVal == "" OR $dateVal == "0000-00-00" OR $dateVal == "NULL") {
          echo "<input type='text' name='$fieldName' size='10' value='mm/dd/yyyy' onClick=\"this.value=''\">";
      } Else {
          echo "<input type='text' name='$fieldName' size='10' value='$dateVal'>";
      }
  }

  Function declareError($bolBold) {
      global $strError;
      If ($strError != "") {
          If ($bolBold == TRUE) {
              echo "<b><font color='red'>$strError</font></b><p>";
          } Else {
              echo "<font color='red'>$strError</font><p>";
          }
      }
  }

  Function declareErrorBack($bolBold) {
      global $strError;
      If ($strError != "") {
          If ($bolBold == TRUE) {
              echo "<b><font color='red'>$strError To alter your input, click the \"back\" button on your browser, make any changes, and submit the form once again. Thank you!</font></b><p>";
          } Else {
              echo "<font color='red'>$strError To alter your input, click the \"back\" button on your browser, make any changes, and submit the form once again. Thank you!</font><p>";
          }
      }
  }

   // Basic authentication: from the manual, Chapter 17
   Function authenticateUser($strUsername, $strPassword) {
        If (($PHP_AUTH_USER != $strUsername ) OR ($PHP_AUTH_PW   != $strPassword)) {
            Header("WWW-Authenticate: Basic realm=\"Authenticate\"");
            Header("HTTP/1.0 401 Unauthorized");
            echo "You entered an invalid login or password.";
            exit;
        }
   }

   // generates random num between min and max
   Function randomNumGen($intMin, $intMax) {
       srand ((double) microtime() * 1000000);
           $intRandom = rand($intMin, $intMax);
           Return $intRandom;
   }

   // picks random character from provided string, or entire alphanumeric set
   // if strChars is null
   Function randomCharGen($strChars) {
       srand ((double) microtime() * 1000000);
       If ($strChars) {
           $strBaseString = $strChars;
           $intMin = 0;
           $intMax = strlen($strChars);
       } Else {
           $strBaseString = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
           $intMin = 0;
           $intMax = 33;
       }

       $intRandom = rand($intMin, $intMax);
       Return substr($strBaseString, $intRandom, 1);
   }

   // hide an integer. This is security by obscurity; use mcrypt library if you have it!!!
   Function numHide($intValue) {
       Return md5(base64_encode($intValue)); 
   }

   // unhide an integer. $intUpperLimit is the maximum that integer could be.
   Function numShow($strValue, $intUpperLimit) {
       for ($i = 0; $i <= $intUpperLimit; $i++) {
           If ($strValue == md5(base64_encode($i))) {
                Return $i;
                break(2);
           }
       }
   }

   Function HTMLuntreat($strValue) {
       # need code for converting ampersands, but only if they are not in an anchor tag.
       $strValue = strip_tags($strValue, "<a><b><i><u><img>"); # 2nd param - allowable tags
       $strValue = str_replace("\n", "<br>", $strValue);
       $strValue = str_replace("  ", " &nbsp;", $strValue);
       Return $strValue;
   }

   Function HTMLtreat($strValue) {
        $strValue = str_replace("<br>", "\n", $strValue);
        $strValue = str_replace(" &nbsp;", "  ", $strValue);
        Return $strValue;
   }

   Function buildStates($strSelectState, $strComboName) {
       $strSelectState = trim($strSelectState);

       echo "<select size='1' name='cbo$strComboName'>";
       echo "<option value=''></option>\r\n";
       echo "<option value='AL'";
       IF ($strSelectState=="AL") {
           echo " selected  ";
       }
       echo ">ALABAMA</option>\r\n";
       echo "<option value='AK'";
       IF ($strSelectState=="AK") {
           echo " selected ";
       }
       echo ">ALASKA</option>\r\n";
       echo "<option value='AZ'";
       IF ($strSelectState=="AZ") {
           echo " selected  ";
       }
       echo ">ARIZONA</option>\r\n";
       echo "<option value='AR'";
       IF ($strSelectState=="AR") {
           echo " selected ";
       }
       echo ">ARKANSAS</option>\r\n";
       echo "<option value='CA'";
       IF ($strSelectState=="CA") {
           echo " selected ";
       }
       echo ">CALIFORNIA</option>\r\n";
       echo "<option value='CO'";
       IF ($strSelectState=="CO") {
           echo " selected ";
       }
       echo ">COLORADO</option>\r\n";
       echo "<option value='CT'";
       IF ($strSelectState=="CT") {
           echo " selected ";
       }
       echo ">CONNECTICUT</option>\r\n";
       echo "<option value='DE'";
       IF ($strSelectState=="DE") {
           echo " selected ";
       }
       echo ">DELAWARE</option>\r\n";
       echo "<option value='DC'";
       IF ($strSelectState=="DC") {
           echo " selected ";
       }
       echo ">DISTRICT OF COLUMBIA</option>\r\n";
       echo "<option value='FL'";
       IF ($strSelectState=="FL") {
           echo " selected ";
       }
       echo ">FLORIDA</option>\r\n";
       echo "<option value='GA'";
       IF ($strSelectState=="GA") {
           echo " selected ";
       }
       echo ">GEORGIA</option>\r\n";
       echo "<option value='HI'";
       IF ($strSelectState=="HI") {
           echo " selected ";
       }
       echo ">HAWAII</option>\r\n";
       echo "<option value='ID'";
       IF ($strSelectState=="ID") {
           echo " selected ";
       }
       echo ">IDAHO</option>\r\n";
       echo "<option value='IL'";
       IF ($strSelectState=="IL") {
           echo " selected ";
       }
       echo ">ILLINOIS</option>\r\n";
       echo "<option value='IN'";
       IF ($strSelectState=="IN") {
           echo " selected ";
       }
       echo ">INDIANA</option>\r\n";
       echo "<option value='IA'";
       IF ($strSelectState=="IA") {
           echo " selected ";
       }
       echo ">IOWA</option>\r\n";
       echo "<option value='KS'";
       IF ($strSelectState=="KS") {
           echo " selected ";
       }
       echo ">KANSAS</option>\r\n";
       echo "<option value='KY'";
       IF ($strSelectState=="KY") {
           echo " selected ";
       }
       echo ">KENTUCKY</option>\r\n";
       echo "<option value='LA'";
       IF ($strSelectState=="LA") {
           echo " selected ";
       }
       echo ">LOUISIANA</option>\r\n";
       echo "<option value='ME'";
       IF ($strSelectState=="ME") {
           echo " selected ";
       }
       echo ">MAINE</option>\r\n";
       echo "<option value='MD'";
       IF ($strSelectState=="MD") {
           echo " selected ";
       }
       echo ">MARYLAND</option>\r\n";
       echo "<option value='MA'";
       IF ($strSelectState=="MA") {
           echo " selected ";
       }
       echo ">MASSACHUSETTS</option>\r\n";
       echo "<option value='MI'";
       IF ($strSelectState=="MI") {
           echo " selected ";
       }
       echo ">MICHIGAN</option>\r\n";
       echo "<option value='MN'";
       IF ($strSelectState=="MN") {
           echo " selected ";
       }
       echo ">MINNESOTA</option>\r\n";
       echo "<option value='MS'";
       IF ($strSelectState=="MS") {
           echo " selected ";
       }
       echo ">MISSISSIPPI</option>\r\n";
       echo "<option value='MO'";
       IF ($strSelectState=="MO") {
           echo " selected ";
       }
       echo ">MISSOURI</option>\r\n";
       echo "<option value='MT'";
       IF ($strSelectState=="MT") {
           echo " selected ";
       }
       echo ">MONTANA</option>\r\n";
       echo "<option value='NE'";
       IF ($strSelectState=="NE") {
           echo " selected ";
       }
       echo ">NEBRASKA</option>\r\n";
       echo "<option value='NV'";
       IF ($strSelectState=="NV") {
           echo " selected ";
       }
       echo ">NEVADA</option>\r\n";
       echo "<option value='NH'";
       IF ($strSelectState=="NH") {
           echo " selected ";
       }
       echo ">NEW HAMPSHIRE</option>\r\n";
       echo "<option value='NJ'";
       IF ($strSelectState=="NJ") {
           echo " selected ";
       }
       echo ">NEW JERSEY</option>\r\n";
       echo "<option value='NM'";
       IF ($strSelectState=="NM") {
           echo " selected ";
       }
       echo ">NEW MEXICO</option>\r\n";
       echo "<option value='NY'";
       IF ($strSelectState=="NY") {
           echo " selected ";
       }
       echo ">NEW YORK</option>\r\n";
       echo "<option value='NC'";
       IF ($strSelectState=="NC") {
           echo " selected ";
       }
       echo ">NORTH CAROLINA</option>\r\n";
       echo "<option value='ND'";
       IF ($strSelectState=="ND") {
           echo " selected ";
       }
       echo ">NORTH DAKOTA</option>\r\n";
       echo "<option value='OH'";
       IF ($strSelectState=="OH") {
           echo " selected ";
       }
       echo ">OHIO</option>\r\n";
       echo "<option value='OK'";
       IF ($strSelectState=="OK") {
           echo " selected ";
       }
       echo ">OKLAHOMA</option>\r\n";
       echo "<option value='OR'";
       IF ($strSelectState=="OR") {
           echo " selected ";
       }
       echo ">OREGON</option>\r\n";
       echo "<option value='PA'";
       IF ($strSelectState=="PA") {
           echo " selected ";
       }
       echo ">PENNSYLVANIA</option>\r\n";
       echo "<option value='PR'";
       IF ($strSelectState=="PR") {
           echo " selected ";
       }
       echo ">PUERTO RICO</option>\r\n";
       echo "<option value='RI'";
       IF ($strSelectState=="RI") {
           echo " selected ";
       }
       echo ">RHODE ISLAND</option>\r\n";
       echo "<option value='SC'";
       IF ($strSelectState=="SC") {
           echo " selected ";
       }
       echo ">SOUTH CAROLINA</option>\r\n";
       echo "<option value='SD'";
       IF ($strSelectState=="SD") {
           echo " selected ";
       }
       echo ">SOUTH DAKOTA</option>\r\n";
       echo "<option value='TN'";
       IF ($strSelectState=="TN") {
           echo " selected ";
       }
       echo ">TENNESSEE</option>\r\n";
       echo "<option value='TX'";
       IF ($strSelectState=="TX") {
           echo " selected ";
       }
       echo ">TEXAS</option>\r\n";
       echo "<option value='UT'";
       IF ($strSelectState=="UT") {
           echo " selected ";
       }
       echo ">UTAH</option>\r\n";
       echo "<option value='VT'";
       IF ($strSelectState=="VT") {
           echo " selected ";
       }
       echo ">VERMONT</option>\r\n";
       echo "<option value='VA'";
       IF ($strSelectState=="VA") {
           echo " selected ";
       }
       echo ">VIRGINIA</option>\r\n";
       echo "<option value='WA'";
       IF ($strSelectState=="WA") {
           echo " selected ";
       }
       echo ">WASHINGTON</option>\r\n";
       echo "<option value='WV'";
       IF ($strSelectState=="WV") {
           echo " selected ";
       }
       echo ">WEST VIRGINIA</option>\r\n";
       echo "<option value='WI'";
       IF ($strSelectState=="WI") {
           echo " selected ";
       }
       echo ">WISCONSIN</option>\r\n";
       echo "<option value='WY'";
       IF ($strSelectState=="WY") {
           echo " selected ";
       }
       echo ">WYOMING</option>\r\n";
       echo "</select>";
   }
?>
