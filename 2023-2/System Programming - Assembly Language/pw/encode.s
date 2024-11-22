.extern encode_chr
print_encode: .global print_encode
	@print_encode(char str[]);
	@             r0

	push	{r4, r7, lr}
	sub		sp, sp, #100 	@char res[100];
							@sp = [res]
	mov		r7, r0			@r7 == [str]
	mov		r4, #0			@int i = 0; (i == r4)
	
loop:
	ldrb	r0, [r7, r4]	@r0 = str[i] (str[i] == [r7, r4])
	cmp		r0, #0			@if(srt[i] == 0) goto exit;
	beq		exit

	bl		encode_chr
	strb	r0, [sp, r4]
	add		r4, r4, #1		@i++;
	b		loop			@goto loop;
exit:
	mov		r0, #1			@write(1, res, i);
	mov		r1, sp			@r7   r0  r1   r2
	mov		r2, r4
	mov		r7, #4			@syscall == write
	swi		0

	add		sp, sp, #100	@sp recover
	pop		{r4, r7, lr}
	bx		lr
