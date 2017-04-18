<?php

class Foo {
	public $aMemberVar = 'aMemberVar Member Variable';
	public $aFuncName = 'aMemberFunc';


	function aMemberFunc() {
		print 'Inside `aMemberFunc()`';
	}
}

$foo = new Foo;

?>

<?php 
$element = 'aMemberVar'; 
print $foo->$element; // prints "aMemberVar Member Variable" 
print '<br/>';
print '<br/>';
?> 


<?php 
function getVarName() 
{ return 'aMemberVar'; } 

print $foo->{getVarName()}; // prints "aMemberVar Member Variable" 
print '<br/>';
print '<br/>';
?> 


<?php 
define(MY_CONSTANT, 'aMemberVar'); 
print $foo->{MY_CONSTANT}; // Prints "aMemberVar Member Variable" 
print $foo->{'aMemberVar'}; // Prints "aMemberVar Member Variable" 
print '<br/>';
print '<br/>';
?> 

<?php 
print $foo->{'aMemberFunc'}(); // Prints "Inside `aMemberFunc()`" 
print $foo->{$foo->aFuncName}(); // Prints "Inside `aMemberFunc()`" 
?>