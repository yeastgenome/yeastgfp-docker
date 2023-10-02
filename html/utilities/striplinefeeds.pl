#!/usr/bin/perl -w

#
# Replaces Mac \r newlines with UNIX \n newlines.
# Uses STDIN for input (ie., redirect file into striplinefeeds.pl).
#

while ($stream=<STDIN>) {
    $stream =~ s/\r/\n/g;
	print "$stream";
}

exit;
