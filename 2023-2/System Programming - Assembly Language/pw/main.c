#include <stdio.h>

char encode_chr(char src);
void print_encode(char str[]);

int main()
{
	char str[100];

	scanf("%s", str);
	print_encode(str);

	printf("\noriginal str: %s\n", str);
}
