.text
_start: .global _start
	@ sys_read r7 = 3
	mov		r0, #0
	ldr 	r1, =buffer
	mov 	r2, #100
	mov 	r7, #3
	swi 	0

	ldrb	r7, [r1]
	sub		r7, r7, #32
	strb	r7, [r1]

	@ sys_write r7 = 4
	mov		r0, #1
	mov 	r2, #100
	mov		r7, #4
	swi		0

	@ sys_eixt r7 = 1
	mov r0, #0
	mov r7, #1
	swi 0

.bss
buffer:
	.skip 101
.end
