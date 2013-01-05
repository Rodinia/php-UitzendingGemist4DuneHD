/*
 *
 * dsf_pack: HDi Dune Media Player service file packer.
 * 
 * Accident 2009: Epoch, encode small files, needs extra work for
 *                encodes over one page (I think) Never tried it.
 *
 * This is woefully incomplete. But it was enough for my requirements.
 *
 * For example: create a file with:
 * #!/bin/sh
 * /usr/sbin/telnetd &
 * exit 0
 *
 * Then pack it up. Note that the file must be named
 * "dune_service_XXXX.dsf" for it to be recognised.
 *
 * When the Dune runs it, it copies it to /tmp, gunzips it as
 * /tmp/service and runs it.
 *
 */

#include <stdio.h>
#include <fcntl.h>
#include <stdint.h>
#include <unistd.h>
#include <stdlib.h>
#include <string.h>


extern int getopt();
extern char *optarg;
extern int optind;

static char *arg_decode      = NULL;
static char *arg_encode      = NULL;
static char *arg_outfilename = NULL;
static char *arg_comparename = NULL;
static int   arg_verbose     = 0;


// XOR block pulled from "shell".
static uint8_t key[] = {
        0xDA, 0xDF, 0xDD, 0x05, 0x53, 0x40, 0x45, 0xb3, 0xee, 0xcc, 0x26, 0x5e, 
        0xb8, 0x0b, 0x25, 0xdb, 0xa2, 0xe6, 0xec, 0x63, 0xf2, 0xe1, 0x19, 0x76, 
        0x08, 0x43, 0x38, 0x6f, 0xc5, 0xc1, 0x85, 0x46
};



static void options(char *prog)
{
        printf("\n");
        printf("%s - Dune .dsf encode/decode program\n\n", prog);
        printf("  options:\n");
        printf("  -h          : display usage help (this output)\n");
        printf("  -d filename : decode .dsf to outfile.gz\n");
        printf("  -e filename : encode infile.gz to .dsf\n");
        printf("  -o filename : specify different output filename\n");
        printf("  -c filename : compare encode with file\n");
        printf("  -v          : verbose it up!\n");
        printf("\n");
        printf("Be aware that dune service files have to be named dune_service_*.dsf\n\n");
        printf("Dune encoding tool by Accident.\nResist the temptation to do bad!\n\n");
        exit(0);
}



void arguments(int argc, char **argv)
{
        int opt;
    
        while ((opt=getopt(argc, argv, 
                                           "hd:e:o:vc:"
                                           )) != -1) {

                switch(opt) {

                case 'h':
                        options(argv[0]);
                        break;
                case 'd':
                        arg_decode = strdup(optarg);
                        break;
                case 'e':
                        arg_encode = strdup(optarg);
                        break;
                case 'o':
                        arg_outfilename = strdup(optarg);
                        break;
                case 'c':
                        arg_comparename = strdup(optarg);
                        break;
                case 'v':
                        arg_verbose++;
                        break;
                }
        }

        if (!arg_decode && !arg_encode)
                options(argv[0]);
}




int main(int argc, char **argv)
{
        FILE *fd = NULL, *out = NULL;
        uint8_t buffer[0x1000], v0,v1,a1,nv0;
        uint32_t red, i, half_red;
        uint32_t decode = 0;


        arguments(argc, argv);

        if (arg_decode) 
                dune_decode(arg_decode);

        if (arg_encode) 
                dune_encode(arg_encode);

}



int dune_decode(char *infilename)
{
        FILE *fd = NULL, *out = NULL;
        uint8_t buffer[0x1000], v0,v1,a1,nv0;
        uint32_t red, i, half_red;
        uint32_t decode = 0;


        fd = fopen(infilename, "rb");
        if (!fd) goto failed;

        out = fopen(arg_outfilename ? arg_outfilename : "outfile.gz", "wb");
        if (!out) goto failed;

        // read header
        if (fread(buffer, 0x141, 1, fd) != 1) goto failed;

        // Read blocks:
        while ((red = fread(buffer, 1, sizeof(buffer), fd)) > 0) {
                // Decode block

                if (arg_verbose)
                        printf("read %x \n", red);

                half_red = red>>1;

                for (i = 0; i < half_red; i++) {

                        v0 = buffer[i + half_red] ;
                        v1 = key[decode];
                        a1 = buffer[ i ];

                        nv0 = v0;
                        nv0 ^= v1;
                        nv0 ^= a1;

                        if (arg_verbose)
                                printf("  [%x]=%02X, xor %02X, [%x]=%02X, storing %02X\n",
                                           i+half_red, v0, v1, i, a1, nv0);

                        buffer[i] = nv0;

                        decode++;
                        if (decode >= sizeof(key))
                                decode = 0;
                }


                for (i = half_red; i < red; i++) {

                        v1 = key[decode];
                        a1 = buffer[ i ];

                        nv0 = v1;
                        nv0 ^= a1;

                        if (arg_verbose)
                                printf("  xor %02X, [%x]=%02X, storing %02X\n",
                                           v1, i, a1, nv0);

                        buffer[i] = nv0;

                        decode++;
                        if (decode >= sizeof(key))
                                decode = 0;
                }


                // Write block
                fwrite(buffer, red, 1, out);

        }

        fclose(fd);
        fclose(out);

        printf("Decode complete\n");

        return 0;

 failed:
        perror(":");
        printf("failed\n");
        if (fd) 
                fclose(fd);
        else
                fprintf(stderr, "Failed to open infile '%s'\r\n", infilename);

        if (out) 
                fclose(out);
        else if (fd)
                fprintf(stderr, "Failed to open outfile '%s'\r\n", 
                                arg_outfilename ? arg_outfilename : "outfile.gz");

}



int dune_encode(char *infilename)
{

        FILE *fd = NULL, *out = NULL, *compare=NULL;
        uint8_t buffer[0x1000], v0,v1,a1,nv0;
        uint8_t compbuf[0x1000];
        uint32_t red, i, half_red;
        uint32_t decode = 0;
        unsigned char stuff[] = {
                0x1d,0x25,0x7a,0xe4,0x27,0xff,0xe9,0x8c,0x58,0x0a,0x8b,0x18,0x70,0xbb,0xf2,0x87,
                0x3a,0xf2,0x41,0x68,0x9e,0x81,0x85,0xc6,0x82,0xe1,0x92,0x7d,0xa0,0x4e,0x5b,0xbd,
                0x73,0xd5,0xa1,0x9a,0xd4,0x8a,0x27,0x2c,0x95,0xb2,0x44,0x05,0x6d,0x37,0x8c,0xa7,
                0x29,0xce,0x0f,0xc7,0x4f,0x94,0x8e,0xd2,0x75,0x20,0x4f,0x15,0x6e,0xaa,0xd3,0xe1,0x7f
        };


        fd = fopen(infilename, "rb");
        if (!fd) goto failed;

        out = fopen(arg_outfilename ? arg_outfilename : "outfile.dsf", "wb");
        if (!out) goto failed;

        if (arg_comparename)
                compare=fopen(arg_comparename, "rb");

        // read header
        fwrite("DUNE SERVICE FILE", 18, 1, out);
        // One day work out what these are.
        fwrite("86673687", 9, 1, out);
        fwrite("54139262", 9, 1, out);
        fwrite("53151487", 9, 1, out);
        fwrite("50852538", 9, 1, out);
        fwrite("38155310", 9, 1, out);
        fwrite("9047468", 8, 1, out);
        fwrite("46935104", 9, 1, out);
        fwrite("64658461", 9, 1, out);
        fwrite("64007208", 9, 1, out);
        fwrite("93556737", 9, 1, out);
        fwrite("88478556", 9, 1, out);
        fwrite("6195915", 8, 1, out);
        fwrite("677883", 7, 1, out);
        fwrite("9851071", 8, 1, out);
        fwrite("82589884", 9, 1, out);
        fwrite("81953245", 9, 1, out);

        // First header thing is 0x100.
        fseek(out, 0x100, SEEK_SET);

        // Then there is 0x41 bytes of something.
        fwrite(stuff, sizeof(stuff), 1, out);


        if (compare)
                fseek(compare, 0x141, SEEK_SET);


        // Read blocks:
        while ((red = fread(buffer, 1, sizeof(buffer), fd)) > 0) {
                // Encode block
                if (arg_verbose)
                        printf("read %x \n", red);

                if (compare)
                        fread(compbuf, 1, red, compare);


                half_red = red>>1;
                decode = half_red % sizeof(key);
                if (arg_verbose) printf("Decode start %d\n", decode);

                for (i = half_red; i < red; i++) {

                        v1 = key[decode];
                        a1 = buffer[ i ];

                        nv0 = v1;
                        nv0 ^= a1;

                        if (arg_verbose)
                                printf("  xor %02X, [%x]=%02X, storing %02X\n",
                                           v1, i, a1, nv0);

                        buffer[i] = nv0;

                        if (compare &&
                                buffer[i] != compbuf[i])
                                printf("Compare buf has %02X\n", compbuf[i]);

                        decode++;
                        if (decode >= sizeof(key))
                                decode = 0;
                }

                decode = 0;
                if (arg_verbose) printf("Halfway point as decode %d\n", decode);

                for (i = 0; i < half_red; i++) {

                        v0 = buffer[i + half_red] ;
                        v1 = key[decode];
                        a1 = buffer[ i ];

                        nv0 = v0;
                        nv0 ^= v1;
                        nv0 ^= a1;

                        if (arg_verbose)
                                printf("  [%x]=%02X, xor %02X, [%x]=%02X, storing %02X\n",
                                           i+half_red, v0, v1, i, a1, nv0);

                        buffer[i] = nv0;

                        if (compare &&
                                buffer[i] != compbuf[i])
                                printf("Compare buf has %02X\n", compbuf[i]);

                        decode++;
                        if (decode >= sizeof(key))
                                decode = 0;
                }

                // Write block
                fwrite(buffer, red, 1, out);


        }

        fclose(fd);
        fclose(out);
        if (compare)
                fclose(compare);

        printf("Encode complete\n");

        return 0;

 failed:
        perror(":");
        printf("failed\n");
        if (fd) 
                fclose(fd);
        else
                fprintf(stderr, "Failed to open infile '%s'\r\n", infilename);

        if (out) 
                fclose(out);
        else if (fd)
                fprintf(stderr, "Failed to open outfile '%s'\r\n", 
                                arg_outfilename ? arg_outfilename : "outfile.gz");

        if (compare)
                fclose(compare);

}