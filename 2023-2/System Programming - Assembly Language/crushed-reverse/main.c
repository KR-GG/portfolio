#include <stdio.h>

int crushed(int a, int b)
{
	return bb(a) + bb(b);
}

int main()
{
	int a = 10, b = 10;
	printf("crushed(%d, %d) = %d\n", a, b, crushed(a, b));
}
