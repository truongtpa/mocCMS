const Ziggy = {"url":"http:\/\/127.0.0.1:8000","port":8000,"defaults":{},"routes":{"BaiVietController.getBaiViet":{"uri":"api\/admin\/bai-viet","methods":["GET","HEAD"]},"DangNhapController.dangNhap":{"uri":"dang-nhap","methods":["GET","HEAD"]},"DangNhapController.callback":{"uri":"dang-nhap\/callback","methods":["GET","HEAD"]},"DangNhapController.dangXuat":{"uri":"dang-xuat","methods":["POST"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
