.text

to_binary: .global to_binary
								@ r0 = num, r1 = [str]
	mov		r2, #0
loop:
	mov		r3, r0, LSL r2		@ num << i
	ands	r3, r3, #0x80000000	@ (num<<i) & 0x80000000
	moveq	r3, #'0'			@ r3 <- '0' (if equal)
	movne	r3, #'1'			@ r3 <- '1' (if not equal)
	strb	r3, [r1, r2]		@ str[i] = '0' or '1' (eq:0, ne:1)
	add		r2, r2, #1			@ r2 = i = i + 1
	cmp		r2, #32				@ if( r2 < 32)
	blt		loop				@ 	goto loop

	mov		r3, #0
	strb	r3, [r1, #32]		@ str[32] = '\0'

	mov		r0, r1				@ return str
	bx		lr					@ return str
