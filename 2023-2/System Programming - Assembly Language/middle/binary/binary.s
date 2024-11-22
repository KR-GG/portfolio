.text
to_binary: .global to_binary
	@ r0 = int num, r1 = char* str
	@ return char* str that consists of 0 or 1
	mov		r2, #0
loop:
	mov		r3, r0, LSL r2
	ands	r3, r3, #0x80000000
	moveq	r3, #'0'
	movne	r3, #'1'
	strb	r3, [r1, r2]
	add		r2, r2, #1
	cmp		r2, #32
	blt		loop
	mov		r3, #0
	strb	r3, [r1, #32]
	mov		r0, r1
	bx		lr
