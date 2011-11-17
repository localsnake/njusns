my @array = (
	'jquery.js',
	'jquery.bgiframe.min.js',
	'jquery.ajaxQueue.js',
	'jquery.autocomplete.min.js',
	'fancybox/jquery.mousewheel-3.0.4.pack.js',
	'fancybox/jquery.fancybox-1.3.4.pack.js',
	'jquery.cookie.js',
	'jquery.hashchange-1.0.0.js',
	'jquery.l10n.min.js',
);

open OUTPUT,">njusns_lib.js";
my $string = "";
foreach (@array) {
	print "PROCESS $_ \n";
	open FH,$_;
	while(<FH>){
		$string .= $_;
	}
	close FH;
	$string .= "\n";
}
print OUTPUT $string;
close OUTPUT;