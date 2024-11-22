#include <stdio.h>

int cap(char* str);

int main()
{
	char text[100];
	scanf("%s", text);
	cap(text);
	printf("%s\n", text);
}
