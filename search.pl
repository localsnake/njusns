my $key = shift @ARGV;
my @php_files = glob '*.html';
my @html_files = glob '*.php';
my @js_files = glob 'scripts/*.js';
my @css_files = glob 'css/*.css';

my @files = (@php_files,@html_files,@js_files,@css_files);
foreach my $filename (@files) {
	open FH , $filename;
	my $string = "";
	my $linecount = 0;
	while(<FH>){
		$linecount ++;
		my $line = $_;
		if($line =~ m/$key/i){
			print "$filename line:$linecount  $line\n";
		}
	}
	close FH;
}
