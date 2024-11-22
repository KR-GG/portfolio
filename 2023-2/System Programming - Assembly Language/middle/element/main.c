#include <stdio.h>

int cap(char* str);
int get_array_elem(char* str, int num);

int main()
{
	char text[100];
	scanf("%s", text);
	cap(text);
	printf("%s\n", text);
	
	char data;
	data = get_array_elem(text, 2);
	printf("third data: %c\n", data);

	return 0;
}
