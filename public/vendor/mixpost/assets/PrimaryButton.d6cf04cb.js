import{o as s,c as o,b as d,f as i,g as a,n,h as c,a as u,C as g,u as p}from"./app.40ac495f.js";const f={class:"bg-white border border-gray-100 rounded-lg"},m={key:0,class:"text-lg font-semibold text-black"},b={key:1,class:"text-gray-500 mt-xs"},v={__name:"Panel",props:{withPadding:{type:Boolean,default:!0}},setup(t){return(e,r)=>(s(),o("div",f,[d("div",{class:n({"p-lg":t.withPadding})},[e.$slots.title?(s(),o("div",m,[i(e.$slots,"title")])):a("",!0),e.$slots.description?(s(),o("div",b,[i(e.$slots,"description")])):a("",!0),d("div",{class:n({"mt-lg":e.$slots.title||e.$slots.description})},[i(e.$slots,"default")],2)],2)]))}},h=["type"],y={key:0,class:"absolute left-0 top-0 flex justify-center items-center w-full h-full bg-indigo-500 rounded-md"},w={__name:"PrimaryButton",props:{type:{type:String,default:"button"},size:{type:String,default:"lg"},isLoading:{type:Boolean,default:!1}},setup(t){const e=t,{sizeClass:r}=c(e.size);return(l,_)=>(s(),o("button",{type:t.type,class:n([p(r),"relative inline-flex items-center bg-indigo-500 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:border-indigo-700 focus:shadow-outline-indigo disabled:bg-indigo-400 transition ease-in-out duration-200"])},[i(l.$slots,"default"),t.isLoading?(s(),o("span",y,[u(g,{class:"animate-spin text-white"})])):a("",!0)],10,h))}};export{v as _,w as a};
