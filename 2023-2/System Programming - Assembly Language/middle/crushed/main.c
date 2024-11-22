#include <stdio.h>

int crushed(int a, int b);

int main() {
	int a, b;
	scanf("%d %d", &a, &b);
	printf("crushed(%d, %d) = %d\n", a, b, crushed(a, b));

	return 0;
}
