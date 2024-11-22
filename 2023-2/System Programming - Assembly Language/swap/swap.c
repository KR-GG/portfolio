extern int buf[];

static int *bufp0;
int *bufp1 = &buf[1];

int swap() {
	int temp;

	bufp0 = &buf[0];
	temp = *bufp0;
	*bufp0 = *bufp1;
	*bufp1 = temp;
}
