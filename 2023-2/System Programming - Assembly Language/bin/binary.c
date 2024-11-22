# include <stdio.h>

char* to_binary(int num, char* str);
/*
{
	for (int i=0; i<32; i++)
	{
		if((num<<i & 0x80000000) == 0)
		{
			str[i] = '0';
		}
		else
		{
			str[i] = '1';
		}
	}
	str[32]='\0';

	return str;
}
*/

int main()
{
	int num;
	char str[33];

	scanf("%d", &num);

	printf("%s\n", to_binary(num, str));
}
