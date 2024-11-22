#include <stdio.h>

char* to_binary(int num, char* str);

int main() {
	int num;
	char str[33];

	scanf("%d", &num);

	printf("%s\n", to_binary(num, str));

	return 0;
}
