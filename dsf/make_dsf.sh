#!/bin/sh

./makeself.sh package dsf_uitzendinggemist.sh "Uitzending Gemist link created in Favorites" ./install.sh

gcc dsf_pack.c -o dsf_pack

gzip dsf_uitzendinggemist.sh 
./dsf_pack -e dsf_uitzendinggemist.sh.gz 
mv outfile.dsf "dune_service_UG@is-great.org.dsf"

rm dsf_uitzendinggemist.sh.gz
