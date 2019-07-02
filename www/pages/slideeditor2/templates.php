<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
	require_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/404.php' );
  //return "papo2";
  exit();
}
require( $_SERVER['DOCUMENT_ROOT'] .'/../include/authenticate.php' );
if (!$_SESSION['logged_in']){
  include ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/403forbidden.php' );
  //return "papo2";
  exit();
}

$_SESSION['LAST_ACTIVITY'] = time();
?>

                <li class="slideElement" data-slide-type="template" data-template-type="radio">
                  <div class="dataHolder ">
                    <div class="pointer-right"></div>
                    <div class="leftSide">
                      <div class="ResizableClass textClass isSelected slideItem" style="left: 0.26666666666666666%; top: 1.4222222222222223%; width: 99%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div>
                        <div id="myInstance_1_1" data-temp-id="1_1" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;"><h1 style="text-align: center;">Ide jön a kérdés amit te teszel fel</h1></div>
                      </div>
                      <div class="ResizableClass textClass isSelected slideItem" style="position: absolute;top: 35.543706597222226%;left: 19.5%;width: 56%;height: auto;">
                        <div class="movingBox"><i class="icon-move"></i></div>
                        <ul class="sortableForm"></ul>
                      </div>
                      <div class="buttonClass disabled" style="left: 87.13333333333333%; top: 91.25925925925927%; width: 12%; height: auto; position: absolute;" ><button type="button" id="submitForm" class="btn btn-dark btn-r">send</button></div>
                    </div>
                    <div class="rightSide"><span class="name">Rádió gombos választás</span></div>
                  </div>
                </li>
                
                <li class="slideElement" data-slide-type="template" data-template-type="check">
                  <div class="dataHolder isSelected">
                    <div class="pointer-right"></div>
                    <div class="leftSide">
                      <div class="ResizableClass textClass isSelected slideItem" style="left: 0.26666666666666666%; top: 1.4222222222222223%; width: 99%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div>
                        <div id="myInstance_2_1" data-temp-id="2_1" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;"><h1 style="text-align: center;">Ide jön a kérdés amit te teszel fel</h1></div>
                      </div>
                      <div class="ResizableClass textClass isSelected slideItem" style="position: absolute;top: 35.543706597222226%;left: 19.5%;width: 56%;height: auto;">
                        <div class="movingBox"><i class="icon-move"></i></div>
                        <ul class="sortableForm"></ul>
                      </div>
                      <div class="buttonClass disabled" style="left: 87.13333333333333%; top: 91.25925925925927%; width: 12%; height: auto; position: absolute;" ><button type="button" id="submitForm" class="btn btn-dark btn-r">send</button></div>
                    </div>
                    <div class="rightSide"><span class="name">Checkbox-os kiválasztás</span></div>
                  </div>
                </li>

                <li class="slideElement" data-slide-type="template" data-template-type="sorting">
                  <div class="dataHolder isSelected">
                    <div class="pointer-right"></div>
                    <div class="leftSide">
                      <div class="ResizableClass textClass isSelected slideItem" style="left: 0.26666666666666666%; top: 1.4222222222222223%; width: 99%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div>
                      <div id="myInstance_4_1" data-temp-id="4_1" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;"><h1 style="text-align: center;">Ide jön a kérdés amit te teszel fel</h1></div></div>
                      <div class="ResizableClass textClass isSelected slideItem" style="position: absolute;top: 35.543706597222226%;left: 19.5%;width: 56%;height: auto;">
                        <div class="movingBox"><i class="icon-move"></i></div>
                        <ul class="sortableForm"></ul>
                      </div>
                      <div class="buttonClass disabled" style="left: 87.13333333333333%; top: 91.25925925925927%; width: 12%; height: auto; position: absolute;" ><button type="button" id="submitForm" class="btn btn-dark btn-r">send</button></div>
                    </div>
                    <div class="rightSide"><span class="name">Sorrend</span></div>
                  </div>
                </li>
                
                <li class="slideElement" data-slide-type="template" data-template-type="groupping">
                  <div class="dataHolder isSelected">
                    <div class="pointer-right"></div>
                    <div class="leftSide">
                      <div class="ResizableClass textClass isSelected slideItem" style="left: 0.26666666666666666%; top: 1.4222222222222223%; width: 99%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div>
                      <div id="myInstance_5_1" data-temp-id="5_1" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;"><h1 style="text-align: center;">Ide jön a kérdés amit te teszel fel</h1></div></div>
                      <div class="nonResizableClass sortableHolder isSelected" style="left: 0%; top: 27.25925925925926%; width: 100%; height: 20%; position: absolute;" data-item-type="Text"><div class="movingBox"><i class="icon-move"></i></div><ul id="sortableHolder"></ul></div>
                      <div class="ResizableClass textClass isSelected slideItem" style="position:absolute;left: 5%; top: 52.148148148148145%%; width: 26%; height: auto;"><div id="myInstance_5_2" data-temp-id="5_2" contenteditable="true" class="textDiv header">New group</div><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><ul class="sortableForm"></ul></div>
                      <div class="buttonClass disabled" style="left: 87.13333333333333%; top: 91.25925925925927%; width: 12%; height: auto; position: absolute;" ><button type="button" id="submitForm" class="btn btn-dark btn-r">send</button></div>
                    </div>
                    <div class="rightSide"><span class="name">Csoportosítás</span></div>
                  </div>
                </li>
                
                <li class="slideElement" data-slide-type="template" data-template-type="pairing">
                  <div class="dataHolder isSelected">
                    <div class="pointer-right"></div>
                    <div class="leftSide">
                      
                      <div class="ResizableClass textClass isSelected slideItem" style="left: 0.26666666666666666%; top: 1.4222222222222223%; width: 99%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div>
                      <div id="myInstance_6_1" data-temp-id="6_1" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;"><h1 style="text-align: center;">Ide jön a kérdés amit te teszel fel</h1></div></div>
                      <div class="ResizableClass textClass isSelected slideItem" style="position:absolute;left: 5%; top: 32%; width: 26%; height: auto;"><div id="myInstance_6_2" data-temp-id="6_2" contenteditable="true" class="textDiv header">Original group</div><div class="movingBox"><i class="icon-move"></i></div><ul class="sortableForm" id="group1"></ul></div>
                      <div class="ResizableClass textClass isSelected slideItem" style="position:absolute;left: 45%; top: 32%; width: 26%; height: auto;"><div id="myInstance_6_3" data-temp-id="6_3" contenteditable="true" class="textDiv header">Sortable group</div><div class="movingBox"><i class="icon-move"></i></div><ul class="sortableForm sortable" id="group2"></ul></div>
                      <div class="buttonClass disabled" style="left: 87.13333333333333%; top: 91.25925925925927%; width: 12%; height: auto; position: absolute;" ><button type="button" id="submitForm" class="btn btn-dark btn-r">send</button></div>
                    </div>
                    <div class="rightSide"><span class="name">Párosítás</span></div>
                  </div>
                </li>
                

                <li class="slideElement" data-slide-type="template" data-template-type="29p_1">
                  <div class="dataHolder isSelected">
                    <div class="pointer-right"></div>
                    <div class="leftSide">
<div class="ResizableClass textClass isSelected slideItem" style="left: 2%; top: 3.7925925925925927%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_8_1" data-temp-id="8_1" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 3.7925925925925927%; left: 78.93333333333334%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>

<div class="ResizableClass textClass isSelected slideItem" style="left: 2%; top: 62.81481481481481%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_8_2" data-temp-id="8_2" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 22.992592592592594%; left: 79.06666666666666%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>

<div class="ResizableClass textClass isSelected slideItem" style="left: 2%; top: 43.851851851851855%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_8_3" data-temp-id="8_3" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 43.851851851851855%; left: 78.93333333333334%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>

<div class="ResizableClass textClass isSelected slideItem" style="left: 2%; top: 23.466666666666665%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_8_4" data-temp-id="8_4" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 62.34074074074074%; left: 79.06666666666666%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>

<div class="ResizableClass textClass isSelected slideItem" style="left: 1.7333333333333332%; top: 81.06666666666666%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_8_5" data-temp-id="8_5" contenteditable="true" class="textDiv " spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 80.35555555555555%; left: 78.93333333333334%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>
                      <div class="buttonClass disabled" style="left: 87.13333333333333%; top: 91.25925925925927%; width: 12%; height: auto; position: absolute;" ><button type="button" id="submitForm" class="btn btn-dark btn-r">send</button></div>
                    </div>
                    <div class="rightSide"><span class="name">Test Step 1 Questions</span></div>
                  </div>
                </li>
                
                <li class="slideElement" data-slide-type="template" data-template-type="29p_2">
                  <div class="dataHolder isSelected">
                    <div class="pointer-right"></div>
                    <div class="leftSide">
<div class="ResizableClass textClass isSelected slideItem" style="left: 2%; top: 3.7925925925925927%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_9_1" data-temp-id="9_1" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 3.7925925925925927%; left: 78.93333333333334%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>

<div class="ResizableClass textClass isSelected slideItem" style="left: 2%; top: 62.81481481481481%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_9_2" data-temp-id="9_2" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 22.992592592592594%; left: 79.06666666666666%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>

<div class="ResizableClass textClass isSelected slideItem" style="left: 2%; top: 43.851851851851855%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_9_3" data-temp-id="9_3" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 43.851851851851855%; left: 78.93333333333334%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>

<div class="ResizableClass textClass isSelected slideItem" style="left: 2%; top: 23.466666666666665%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_9_4" data-temp-id="9_4" contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 62.34074074074074%; left: 79.06666666666666%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>

<div class="ResizableClass textClass isSelected slideItem" style="left: 1.7333333333333332%; top: 81.06666666666666%; width: 74%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_9_5" data-temp-id="9_5" contenteditable="true" class="textDiv " spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
<div class="ResizableClass textClass isSelected slideItem" style="position: absolute; top: 80.35555555555555%; left: 78.93333333333334%; width: 19%; height: auto;"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="holder"><select name=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div>

<div class="buttonClass disabled" style="left: 87.13333333333333%; top: 91.25925925925927%; width: 12%; height: auto; position: absolute;" ><button type="button" id="submitForm" class="btn btn-dark btn-r">send</button></div>
                    </div>
                    <div class="rightSide"><span class="name">Test Step 2 Last</span></div>
                  </div>
                </li>
                
                <li class="slideElement" data-slide-type="template" data-template-type="29p_3">
                  <div class="dataHolder isSelected">
                    <div class="pointer-right"></div>
                    <div class="leftSide">

<div class="textClass ResizableClass isSelected slideItem" style="left: 2.1333333333333333%; top: 4.266666666666667%; width: 21%; height: auto; position: absolute;" data-item-type="Text"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_10_1" data-temp-id="10_1" class="textDiv" contenteditable="true" onpaste="handlepaste(this, event)" style="position: relative;"><h1><b>Érékelés</b></h1></div></div>

<div class="textClass ResizableClass isSelected slideItem" style="left: 2.2666666666666666%; top: 20.622222222222224%; width: 35%; height: auto; position: absolute;" data-item-type="Text"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_10_2" data-temp-id="10_2" class="textDiv" contenteditable="true" onpaste="handlepaste(this, event)" style="position: relative;"><font class="fontSize20"><b>A teszten elért pontszám:</b></font></div></div>
<div class="textClass ResizableClass isSelected slideItem" style="left: 41.46666666666667%; top: 20.622222222222224%; width: 21%; height: auto; position: absolute;" data-item-type="Text"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div class="textDiv" style="position: relative;"><font class="fontSize20" color="#ff0000"><b id="testPointDiv">260 pont</b></font></div></div>

<div class="textClass ResizableClass isSelected slideItem" style="left: 2.2666666666666666%; top: 33.5%; width: 71%; height: auto; position: absolute;" data-item-type="Text"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_10_3" data-temp-id="10_3" class="textDiv" contenteditable="true" onpaste="handlepaste(this, event)" style="position: relative;">
<font class="fontSize20"><b>260 pont: Az üzleti kapcsolatépítés nagymestere</b></font><br></div></div>

<div class="ResizableClass textClass isSelected slideItem" style="left: 2%; top: 46.5%; width: 96%; height: auto; position: absolute;"><div class="movingBox"><i class="icon-move"></i></div><div id="myInstance_10_4" data-temp-id="10_4 contenteditable="true" class="textDiv" spellcheck="false" tabindex="0" style="position: relative;" onpaste="handlepaste(this, event)"><p><font class="fontSize16">Q1</font><br></p></div></div>
                    </div>
                    <div class="rightSide"><span class="name">Test Step 3 Result</span></div>
                  </div>
                </li>
                
                
                